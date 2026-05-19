<?php
/**
 * Modelo Prestamo — Biblioteca Fusalmo
 * CRUD completo para gestión de préstamos
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

class Prestamo {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene todos los préstamos.
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT p.id, p.usuario_id, u.nombre AS usuario, p.libro_id, l.titulo AS libro,
                    p.fecha_prestamo, p.fecha_devolucion_esperada, p.fecha_devolucion_real,
                    p.estado, p.multa
             FROM prestamos p
             JOIN usuarios u ON p.usuario_id = u.id
             JOIN libros l ON p.libro_id = l.id
             ORDER BY p.fecha_prestamo DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un préstamo por ID.
     */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.usuario_id, u.nombre AS usuario, p.libro_id, l.titulo AS libro,
                    p.fecha_prestamo, p.fecha_devolucion_esperada, p.fecha_devolucion_real,
                    p.estado, p.multa
             FROM prestamos p
             JOIN usuarios u ON p.usuario_id = u.id
             JOIN libros l ON p.libro_id = l.id
             WHERE p.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene préstamos activos de un usuario.
     */
    public function getByUsuarioActivos(int $usuarioId): array {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.libro_id, l.titulo, p.fecha_prestamo, 
                    p.fecha_devolucion_esperada, 
                    DATEDIFF(CURDATE(), p.fecha_devolucion_esperada) AS dias_atraso,
                    p.multa
             FROM prestamos p
             JOIN libros l ON p.libro_id = l.id
             WHERE p.usuario_id = :usuario_id AND p.estado = 'activo'
             ORDER BY p.fecha_devolucion_esperada"
        );
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene préstamos por estado.
     */
    public function getByEstado(string $estado): array {
        $stmt = $this->db->prepare(
            "SELECT p.id, u.nombre AS usuario, l.titulo AS libro, 
                    p.fecha_prestamo, p.fecha_devolucion_esperada, p.estado
             FROM prestamos p
             JOIN usuarios u ON p.usuario_id = u.id
             JOIN libros l ON p.libro_id = l.id
             WHERE p.estado = :estado
             ORDER BY p.fecha_prestamo DESC"
        );
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll();
    }

    /**
     * Verifica si un usuario puede hacer préstamos (límite de 3 activos).
     */
    public function puedePrestar(int $usuarioId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM prestamos WHERE usuario_id = :usuario_id AND estado = 'activo'"
        );
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchColumn() < 3;
    }

    /**
     * Verifica si el usuario tiene o tuvo algún préstamo del libro indicado.
     */
    public function tienePrestamoLibro(int $usuarioId, int $libroId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM prestamos
             WHERE usuario_id = :usuario_id AND libro_id = :libro_id"
        );
        $stmt->execute([':usuario_id' => $usuarioId, ':libro_id' => $libroId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crea un nuevo préstamo.
     */
    public function create(array $data): bool {
        $usuarioId = Security::sanitizeInt($data['usuario_id'] ?? $data['usuarioId'] ?? 0);
        $libroId = Security::sanitizeInt($data['libro_id'] ?? $data['libroId'] ?? 0);

        $stmt = $this->db->prepare(
            "INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion_esperada, estado, created_at)
             VALUES (:usuario_id, :libro_id, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'activo', NOW())"
        );
        $result = $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':libro_id'   => $libroId,
        ]);

        // Si se crea exitosamente, decrementar cantidad y actualizar estado
        if ($result) {
            $this->db->prepare(
                "UPDATE libros
                 SET cantidad = cantidad - 1,
                     estado = CASE WHEN (cantidad - 1) <= 0 THEN 'agotado' ELSE 'disponible' END
                 WHERE id = :id"
            )->execute([':id' => $libroId]);
        }

        return $result;
    }

    /**
     * Registra la devolución de un libro.
     */
    public function procesarDevolucion(int $prestamoId, bool $conMulta = false): bool {
        $multa = $conMulta ? 50000 : 0; // 50000 pesos de multa si está atrasado

        $stmt = $this->db->prepare(
            "UPDATE prestamos 
             SET fecha_devolucion_real = NOW(),
                 estado = 'devuelto',
                 multa = :multa
             WHERE id = :id"
        );
        $result = $stmt->execute([':id' => $prestamoId, ':multa' => $multa]);

        // Si se devuelve exitosamente, incrementar cantidad y actualizar estado
        if ($result) {
            $prestamo = $this->getById($prestamoId);
            if ($prestamo) {
                $libroId = $prestamo['libro_id'];
                $this->db->prepare(
                    "UPDATE libros
                     SET cantidad = cantidad + 1,
                         estado = 'disponible'
                     WHERE id = :id"
                )->execute([':id' => $libroId]);
            }
        }

        return $result;
    }

    /**
     * Renueva un préstamo (extiende 14 días más).
     */
    public function renovar(int $prestamoId): bool {
        $stmt = $this->db->prepare(
            "UPDATE prestamos 
             SET fecha_devolucion_esperada = DATE_ADD(fecha_devolucion_esperada, INTERVAL 14 DAY)
             WHERE id = :id AND estado = 'activo'"
        );
        return $stmt->execute([':id' => $prestamoId]);
    }

    /**
     * Obtiene préstamos vencidos.
     */
    public function getPrestamosVencidos(): array {
        $stmt = $this->db->query(
            "SELECT p.id, u.nombre AS usuario, l.titulo AS libro, 
                    p.fecha_devolucion_esperada,
                    DATEDIFF(CURDATE(), p.fecha_devolucion_esperada) AS dias_atraso
             FROM prestamos p
             JOIN usuarios u ON p.usuario_id = u.id
             JOIN libros l ON p.libro_id = l.id
             WHERE p.estado = 'activo' AND p.fecha_devolucion_esperada < CURDATE()
             ORDER BY p.fecha_devolucion_esperada"
        );
        return $stmt->fetchAll();
    }

    /**
     * Calcula multas pendientes de un usuario.
     */
    public function getMultasPendientes(int $usuarioId): int {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(multa), 0) FROM prestamos 
             WHERE usuario_id = :usuario_id AND estado = 'devuelto' AND multa > 0"
        );
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchColumn();
    }
}
