<?php
/**
 * Controlador de Préstamos — Biblioteca Fusalmo
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Calificacion.php';

class PrestamoController {

    private Prestamo $prestamoModel;
    private Libro $libroModel;
    private Usuario $usuarioModel;

    public function __construct() {
        Session::start();
        $this->prestamoModel = new Prestamo();
        $this->libroModel = new Libro();
        $this->usuarioModel = new Usuario();
    }

    public function index(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        $prestamos = $this->prestamoModel->getAll();
        require_once __DIR__ . '/../views/prestamos/index.php';
    }

    public function showCreate(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        $usuarios = $this->usuarioModel->getAll();
        $libros = $this->libroModel->getAll();
        require_once __DIR__ . '/../views/prestamos/create.php';
    }

    public function processCreate(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        Security::validateCSRF();

        $usuarioId = Security::sanitizeInt($_POST['usuario_id'] ?? 0);
        $libroId = Security::sanitizeInt($_POST['libro_id'] ?? 0);

        $errors = [];

        if ($usuarioId <= 0) $errors[] = 'Usuario es requerido.';
        if ($libroId <= 0) $errors[] = 'Libro es requerido.';
        
        $usuario = $usuarioId > 0 ? $this->usuarioModel->getById($usuarioId) : false;
        $libro = $libroId > 0 ? $this->libroModel->getById($libroId) : false;

        if (!$usuario) $errors[] = 'Usuario no encontrado.';
        if ($usuario && ($usuario['estado'] ?? 'activo') !== 'activo') $errors[] = 'El usuario está inactivo y no puede recibir préstamos.';
        if (!$libro) $errors[] = 'Libro no encontrado.';
        if ($libro && $libro['cantidad'] <= 0) $errors[] = 'No hay copias disponibles de este libro.';
        if ($usuario && !$this->prestamoModel->puedePrestar($usuarioId)) {
            $errors[] = 'Usuario ya tiene 3 préstamos activos (límite máximo).';
        }

        if (!empty($errors)) {
            $_SESSION['prest_errors'] = $errors;
            header('Location: index.php?page=prestamos&action=create');
            exit();
        }

        if ($this->prestamoModel->create(['usuario_id' => $usuarioId, 'libro_id' => $libroId])) {
            $_SESSION['success'] = 'Préstamo registrado exitosamente.';
            header('Location: index.php?page=prestamos');
        } else {
            $_SESSION['error'] = 'Error al crear préstamo.';
            header('Location: index.php?page=prestamos&action=create');
        }
        exit();
    }

    public function procesarDevolucion(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        Security::validateCSRF();

        $prestamoId = Security::sanitizeInt($_POST['id'] ?? 0);
        if (!$prestamoId || !$this->prestamoModel->getById($prestamoId)) {
            $_SESSION['error'] = 'Préstamo no encontrado.';
        } else {
            $prestamo = $this->prestamoModel->getById($prestamoId);
            $conMulta = strtotime($prestamo['fecha_devolucion_esperada']) < time();

            if ($this->prestamoModel->procesarDevolucion($prestamoId, $conMulta)) {
                if ($conMulta) {
                    $_SESSION['success'] = 'Devolución registrada. ⚠️ Se aplicó multa por atraso.';
                } else {
                    $_SESSION['success'] = 'Devolución registrada correctamente.';
                }
            } else {
                $_SESSION['error'] = 'Error al procesar devolución.';
            }
        }

        header('Location: index.php?page=prestamos');
        exit();
    }

    public function renovar(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        Security::validateCSRF();

        $prestamoId = Security::sanitizeInt($_POST['id'] ?? 0);
        if (!$prestamoId || !$this->prestamoModel->getById($prestamoId)) {
            $_SESSION['error'] = 'Préstamo no encontrado.';
        } else {
            if ($this->prestamoModel->renovar($prestamoId)) {
                $_SESSION['success'] = 'Préstamo renovado por 14 días más.';
            } else {
                $_SESSION['error'] = 'Error al renovar préstamo.';
            }
        }

        header('Location: index.php?page=prestamos');
        exit();
    }

    public function vencidos(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        $vencidos = $this->prestamoModel->getPrestamosVencidos();
        require_once __DIR__ . '/../views/prestamos/vencidos.php';
    }

    public function misPrestamos(): void {
        Session::requireLogin();
        $usuarioId = (int)$_SESSION['user_id'];
        $prestamos = $this->prestamoModel->getByUsuarioActivos($usuarioId);
        $multas    = $this->prestamoModel->getMultasPendientes($usuarioId);

        $calModel = new Calificacion();
        $misCalificaciones = $calModel->getUserRatings($usuarioId);

        require_once __DIR__ . '/../views/prestamos/mis-prestamos.php';
    }

    // ─── CALIFICAR LIBRO DESDE MIS PRÉSTAMOS ──────────────────────────────

    public function calificarLibro(): void {
        Session::requireRole(['usuario', 'bibliotecario']);
        Security::validateCSRF();

        $libroId   = Security::sanitizeInt($_POST['libro_id']  ?? 0);
        $estrellas = Security::sanitizeInt($_POST['estrellas'] ?? 0);
        $usuarioId = (int)Session::get('user_id');

        if ($libroId <= 0 || $estrellas < 1 || $estrellas > 5) {
            $_SESSION['cal_error'] = 'Calificación inválida. Selecciona entre 1 y 5 estrellas.';
            header('Location: index.php?page=mis-prestamos');
            exit();
        }

        // Verificar que el usuario tenga o haya tenido este libro en préstamo
        if (!$this->prestamoModel->tienePrestamoLibro($usuarioId, $libroId)) {
            $_SESSION['cal_error'] = 'Solo puedes calificar libros que hayas tenido en préstamo.';
            header('Location: index.php?page=mis-prestamos');
            exit();
        }

        $calModel = new Calificacion();
        $calModel->rate($usuarioId, $libroId, $estrellas);

        $_SESSION['cal_success'] = 'Tu calificación fue guardada exitosamente.';
        header('Location: index.php?page=mis-prestamos');
        exit();
    }
}
