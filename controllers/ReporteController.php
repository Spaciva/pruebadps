<?php
/**
 * Controlador de Reportes — Biblioteca Fusalmo
 * Acceso exclusivo para rol administrador.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../models/ReporteMensual.php';

class ReporteController {

    private ReporteMensual $reporteModel;

    public function __construct() {
        Session::start();
        $this->reporteModel = new ReporteMensual();
    }

    // ── VISTA PRINCIPAL ──────────────────────────────────────────────────────

    /**
     * Muestra los datos en vivo del mes seleccionado + historial de reportes guardados.
     */
    public function index(): void {
        Session::requireRole(['admin']);

        $anio = (int) date('Y');
        $mes  = (int) date('n');

        if (!empty($_GET['anio']) && !empty($_GET['mes'])) {
            $anioFiltro = Security::sanitizeInt($_GET['anio']);
            $mesFiltro  = Security::sanitizeInt($_GET['mes']);
            if ($mesFiltro >= 1 && $mesFiltro <= 12 && $anioFiltro >= 2000 && $anioFiltro <= (int) date('Y')) {
                $anio = $anioFiltro;
                $mes  = $mesFiltro;
            }
        }

        $datos      = $this->reporteModel->generarDatos($anio, $mes);
        $historial  = $this->reporteModel->getHistorial(6);
        $yaGuardado = $this->reporteModel->existeReporte($anio, $mes);

        require_once __DIR__ . '/../views/reportes/index.php';
    }

    // ── GUARDAR REPORTE ──────────────────────────────────────────────────────

    /**
     * Fija/guarda el reporte del mes indicado en la base de datos.
     */
    public function guardar(): void {
        Session::requireRole(['admin']);
        Security::validateCSRF();

        $anio   = Security::sanitizeInt($_POST['anio'] ?? (int) date('Y'));
        $mes    = Security::sanitizeInt($_POST['mes']  ?? (int) date('n'));
        $userId = (int) Session::get('user_id');

        if ($mes < 1 || $mes > 12 || $anio < 2000) {
            $_SESSION['error'] = 'Parámetros de mes/año inválidos.';
            header('Location: index.php?page=reportes');
            exit();
        }

        $datos = $this->reporteModel->generarDatos($anio, $mes);

        if ($this->reporteModel->guardar($anio, $mes, $datos, $userId)) {
            $_SESSION['success'] = 'Reporte de ' . $this->nombreMes($mes) . ' ' . $anio . ' guardado correctamente.';
        } else {
            $_SESSION['error'] = 'Error al guardar el reporte. Inténtalo de nuevo.';
        }

        header('Location: index.php?page=reportes&anio=' . $anio . '&mes=' . $mes);
        exit();
    }

    // ── VER REPORTE GUARDADO ─────────────────────────────────────────────────

    /**
     * Muestra un reporte guardado previamente.
     */
    public function ver(): void {
        Session::requireRole(['admin']);

        $id      = Security::sanitizeInt($_GET['id'] ?? 0);
        $reporte = $id > 0 ? $this->reporteModel->getById($id) : false;

        if (!$reporte) {
            $_SESSION['error'] = 'Reporte no encontrado.';
            header('Location: index.php?page=reportes');
            exit();
        }

        require_once __DIR__ . '/../views/reportes/ver.php';
    }

    // ── EXPORTAR / IMPRIMIR ──────────────────────────────────────────────────

    /**
     * Genera una vista limpia para imprimir/guardar como PDF.
     * Si se pasa ?id=X usa el reporte guardado; si no, genera datos en vivo.
     */
    public function exportar(): void {
        Session::requireRole(['admin']);

        $id = Security::sanitizeInt($_GET['id'] ?? 0);

        if ($id > 0) {
            $row = $this->reporteModel->getById($id);
            if (!$row) {
                header('Location: index.php?page=reportes');
                exit();
            }
            $reporte = [
                'anio'                => $row['anio'],
                'mes'                 => $row['mes'],
                'datos'               => $row['datos'],
                'generado_por_nombre' => $row['generado_por_nombre'],
                'created_at'          => $row['created_at'],
            ];
        } else {
            $anio = Security::sanitizeInt($_GET['anio'] ?? (int) date('Y'));
            $mes  = Security::sanitizeInt($_GET['mes']  ?? (int) date('n'));
            $reporte = [
                'anio'                => $anio,
                'mes'                 => $mes,
                'datos'               => $this->reporteModel->generarDatos($anio, $mes),
                'generado_por_nombre' => Session::get('user_name'),
                'created_at'          => date('Y-m-d H:i:s'),
            ];
        }

        require_once __DIR__ . '/../views/reportes/exportar.php';
    }

    // ── UTILIDADES ───────────────────────────────────────────────────────────

    public static function nombreMes(int $mes): string {
        return ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'][$mes] ?? '';
    }
}
