<?php
/**
 * Controlador de Usuarios — Biblioteca Fusalmo
 * Gestiona CRUD completo de usuarios (solo admin)
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {

    private Usuario $usuarioModel;

    public function __construct() {
        Session::start();
        $this->usuarioModel = new Usuario();
    }

    // ─── LISTAR USUARIOS ──────────────────────────────────────────────────────

    /**
     * Muestra la lista de todos los usuarios (solo admin).
     */
    public function index(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        $usuarios = $this->usuarioModel->getAll();
        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    // ─── EDITAR USUARIO ───────────────────────────────────────────────────────

    /**
     * Muestra el formulario de edición de usuario (solo admin).
     */
    public function showEdit(): void {
        Session::requireRole(['admin']);
        
        $id = Security::sanitizeInt($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Usuario no especificado.';
            header('Location: index.php?page=usuarios');
            exit();
        }

        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header('Location: index.php?page=usuarios');
            exit();
        }

        require_once __DIR__ . '/../views/usuarios/edit.php';
    }

    /**
     * Procesa la actualización de un usuario.
     */
    public function processEdit(): void {
        Session::requireRole(['admin']);
        Security::validateCSRF();

        $id       = Security::sanitizeInt($_POST['id'] ?? 0);
        $nombre   = Security::sanitizeString($_POST['nombre'] ?? '');
        $email    = Security::sanitizeEmail($_POST['correo'] ?? '');
        $telefono = Security::sanitizeString($_POST['telefono'] ?? '');
        $rol      = Security::sanitizeString($_POST['rol'] ?? 'usuario');

        $errors = [];

        if (!$id) {
            $errors[] = 'Usuario no especificado.';
        } else {
            $usuario = $this->usuarioModel->getById($id);
            if (!$usuario) $errors[] = 'Usuario no encontrado.';
        }

        if (strlen($nombre) < 3) $errors[] = 'El nombre debe tener al menos 3 caracteres.';
        if (!$email) $errors[] = 'Correo electrónico inválido.';
        if (!in_array($rol, ['admin', 'bibliotecario', 'usuario'])) $errors[] = 'Rol inválido.';

        if ($this->usuarioModel->emailExistsExcept($email, $id)) {
            $errors[] = 'El correo electrónico ya está registrado por otro usuario.';
        }

        if (!empty($errors)) {
            $_SESSION['edit_errors'] = $errors;
            $_SESSION['edit_data'] = ['id' => $id, 'nombre' => $nombre, 'correo' => $email, 'telefono' => $telefono, 'rol' => $rol];
            header("Location: index.php?page=usuarios&action=edit&id={$id}");
            exit();
        }

        $updated = $this->usuarioModel->update($id, [
            'nombre'  => $nombre,
            'correo'  => $email,
            'telefono' => $telefono,
            'rol'     => $rol,
        ]);

        if ($updated) {
            $_SESSION['success'] = 'Usuario actualizado correctamente.';
            header('Location: index.php?page=usuarios');
        } else {
            $_SESSION['error'] = 'Error al actualizar el usuario.';
            header("Location: index.php?page=usuarios&action=edit&id={$id}");
        }
        exit();
    }

    // ─── CAMBIAR CONTRASEÑA ───────────────────────────────────────────────────

    /**
     * Muestra el formulario para cambiar contraseña.
     */
    public function showChangePassword(): void {
        Session::requireRole(['admin']);
        
        $id = Security::sanitizeInt($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Usuario no especificado.';
            header('Location: index.php?page=usuarios');
            exit();
        }

        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header('Location: index.php?page=usuarios');
            exit();
        }

        require_once __DIR__ . '/../views/usuarios/change-password.php';
    }

    /**
     * Procesa el cambio de contraseña.
     */
    public function processChangePassword(): void {
        Session::requireRole(['admin']);
        Security::validateCSRF();

        $id        = Security::sanitizeInt($_POST['id'] ?? 0);
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['confirm'] ?? '';

        $errors = [];

        if (!$id) {
            $errors[] = 'Usuario no especificado.';
        } else {
            $usuario = $this->usuarioModel->getById($id);
            if (!$usuario) $errors[] = 'Usuario no encontrado.';
        }

        if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        if (!preg_match('/[A-Z]/', $password)) $errors[] = 'La contraseña debe tener al menos una mayúscula.';
        if (!preg_match('/[0-9]/', $password)) $errors[] = 'La contraseña debe tener al menos un número.';
        if ($password !== $confirm) $errors[] = 'Las contraseñas no coinciden.';

        if (!empty($errors)) {
            $_SESSION['pass_errors'] = $errors;
            header("Location: index.php?page=usuarios&action=change-password&id={$id}");
            exit();
        }

        $updated = $this->usuarioModel->updatePassword($id, $password);

        if ($updated) {
            $_SESSION['success'] = 'Contraseña actualizada correctamente.';
            header('Location: index.php?page=usuarios');
        } else {
            $_SESSION['error'] = 'Error al actualizar la contraseña.';
            header("Location: index.php?page=usuarios&action=change-password&id={$id}");
        }
        exit();
    }

    // ─── CAMBIAR ESTADO ───────────────────────────────────────────────────────

    /**
     * Cambia el estado de un usuario (activo/inactivo).
     */
    public function toggleStatus(): void {
        Session::requireRole(['admin']);
        Security::validateCSRF();

        $id = Security::sanitizeInt($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Usuario no especificado.';
            header('Location: index.php?page=usuarios');
            exit();
        }

        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header('Location: index.php?page=usuarios');
            exit();
        }

        $nuevoEstado = $usuario['estado'] === 'activo' ? 'inactivo' : 'activo';
        $updated = $this->usuarioModel->updateStatus($id, $nuevoEstado);

        if ($updated) {
            $_SESSION['success'] = "Usuario marcado como {$nuevoEstado}.";
        } else {
            $_SESSION['error'] = 'Error al cambiar el estado.';
        }

        header('Location: index.php?page=usuarios');
        exit();
    }

    // ─── ELIMINAR USUARIO ─────────────────────────────────────────────────────

    /**
 * Elimina un usuario de forma permanente.
 */
public function delete(): void {

    Session::requireRole(['admin']);
    Security::validateCSRF();

    $id = Security::sanitizeInt(
        $_POST['id'] ?? 0
    );

    if(!$id){

        $_SESSION['error'] =
        'Usuario no especificado.';

        header(
        'Location: index.php?page=usuarios'
        );

        exit();
    }

    $usuario =
    $this->usuarioModel->getById($id);

    if(!$usuario){

        $_SESSION['error'] =
        'Usuario no encontrado.';

        header(
        'Location: index.php?page=usuarios'
        );

        exit();
    }

    // Evitar eliminarse a sí mismo

    if(
        $usuario['id']
        ==
        $_SESSION['user_id']
    ){

        $_SESSION['error'] =
        'No puedes eliminar tu propia cuenta.';

        header(
        'Location: index.php?page=usuarios'
        );

        exit();
    }

    // NUEVA VALIDACIÓN

    if(
        $this->usuarioModel
        ->hasPrestamos($id)
    ){

        $_SESSION['error'] =
        'No se puede eliminar el usuario porque tiene préstamos asociados.';

        header(
        'Location: index.php?page=usuarios'
        );

        exit();
    }

    $deleted =
    $this->usuarioModel->delete($id);

    if($deleted){

        $_SESSION['success'] =
        'Usuario eliminado correctamente';

    }else{

        $_SESSION['error'] =
        'Error al eliminar el usuario';
    }

    header(
    'Location: index.php?page=usuarios'
    );

    exit();
}
}
