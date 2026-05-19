<?php
/**
 * Modelo ReporteMensual — Biblioteca Fusalmo
 * Genera y persiste reportes estadísticos mensuales.
 */

require_once __DIR__ . '/../config/db.php';

class ReporteMensual {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ── GENERACIÓN EN VIVO ───────────────────────────────────────────────────

    /**
     * Reúne todos los indicadores para un mes/año dado (consultas en vivo).
     */
    public function generarDatos(int $anio, int $mes): array {
        return [
            'total_prestamos'    => $this->totalPrestamos($anio, $mes),
            'prestamos_vencidos' => $this->prestamosVencidos($anio, $mes),
            'total_multas'       => $this->totalMultas($anio, $mes),
            'devoluciones'       => $this->devoluciones($anio, $mes),
            'nuevos_usuarios'    => $this->nuevosUsuarios($anio, $mes),
            'libros_top5'        => $this->librosMasPrestados($anio, $mes),
            'libros_por_estado'  => $this->librosPorEstado(),
            'categorias_activas' => $this->categoriasActivas($anio, $mes),
        ];
    }

    private function totalPrestamos(int $anio, int $mes): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM prestamos
             WHERE YEAR(fecha_prestamo) = :anio
               AND MONTH(fecha_prestamo) = :mes"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return (int) $stmt->fetchColumn();
    }

    private function prestamosVencidos(int $anio, int $mes): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM prestamos
             WHERE estado = 'vencido'
               AND YEAR(fecha_prestamo) = :anio
               AND MONTH(fecha_prestamo) = :mes"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return (int) $stmt->fetchColumn();
    }

    private function totalMultas(int $anio, int $mes): float {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(multa), 0) FROM prestamos
             WHERE multa > 0
               AND YEAR(fecha_prestamo) = :anio
               AND MONTH(fecha_prestamo) = :mes"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return (float) $stmt->fetchColumn();
    }

    private function devoluciones(int $anio, int $mes): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM prestamos
             WHERE estado = 'devuelto'
               AND YEAR(fecha_devolucion_real) = :anio
               AND MONTH(fecha_devolucion_real) = :mes"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return (int) $stmt->fetchColumn();
    }

    private function nuevosUsuarios(int $anio, int $mes): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM usuarios
             WHERE YEAR(created_at) = :anio
               AND MONTH(created_at) = :mes"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return (int) $stmt->fetchColumn();
    }

    private function librosMasPrestados(int $anio, int $mes): array {
        $stmt = $this->db->prepare(
            "SELECT l.titulo, COALESCE(a.nombre, 'Sin autor') AS autor, COUNT(p.id) AS total_prestamos
             FROM prestamos p
             JOIN libros l ON p.libro_id = l.id
             LEFT JOIN autores a ON l.autor_id = a.id
             WHERE YEAR(p.fecha_prestamo) = :anio
               AND MONTH(p.fecha_prestamo) = :mes
             GROUP BY p.libro_id
             ORDER BY total_prestamos DESC
             LIMIT 5"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function librosPorEstado(): array {
        $stmt = $this->db->query(
            "SELECT estado, COUNT(*) AS total FROM libros GROUP BY estado ORDER BY total DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function categoriasActivas(int $anio, int $mes): array {
        $stmt = $this->db->prepare(
            "SELECT c.nombre, COUNT(p.id) AS total_prestamos
             FROM prestamos p
             JOIN libros l ON p.libro_id = l.id
             JOIN categorias c ON l.categoria_id = c.id
             WHERE YEAR(p.fecha_prestamo) = :anio
               AND MONTH(p.fecha_prestamo) = :mes
             GROUP BY c.id
             ORDER BY total_prestamos DESC
             LIMIT 5"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── PERSISTENCIA ─────────────────────────────────────────────────────────

    /**
     * Guarda o sobreescribe el reporte de un mes/año.
     */
    public function guardar(int $anio, int $mes, array $datos, int $userId): bool {
        $json = json_encode($datos, JSON_UNESCAPED_UNICODE);
        $stmt = $this->db->prepare(
            "INSERT INTO reportes_mensuales (anio, mes, datos, generado_por, created_at)
             VALUES (:anio, :mes, :datos, :generado_por, NOW())
             ON DUPLICATE KEY UPDATE
                 datos        = VALUES(datos),
                 generado_por = VALUES(generado_por),
                 updated_at   = NOW()"
        );
        return $stmt->execute([
            ':anio'         => $anio,
            ':mes'          => $mes,
            ':datos'        => $json,
            ':generado_por' => $userId,
        ]);
    }

    /**
     * Devuelve los últimos $limit reportes guardados (más recientes primero).
     */
    public function getHistorial(int $limit = 6): array {
        $stmt = $this->db->prepare(
            "SELECT r.id, r.anio, r.mes, r.created_at, u.nombre AS generado_por
             FROM reportes_mensuales r
             LEFT JOIN usuarios u ON r.generado_por = u.id
             ORDER BY r.anio DESC, r.mes DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un reporte guardado por ID con sus datos decodificados.
     */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.nombre AS generado_por_nombre
             FROM reportes_mensuales r
             LEFT JOIN usuarios u ON r.generado_por = u.id
             WHERE r.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $row['datos'] = json_decode($row['datos'], true);
        }
        return $row;
    }

    /**
     * Verifica si ya existe un reporte guardado para ese mes/año.
     */
    public function existeReporte(int $anio, int $mes): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reportes_mensuales
             WHERE anio = :anio AND mes = :mes"
        );
        $stmt->execute([':anio' => $anio, ':mes' => $mes]);
        return (bool) $stmt->fetchColumn();
    }
}
