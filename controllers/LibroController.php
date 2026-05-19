<?php
/**
 * Controlador de Libros — Biblioteca Fusalmo
 * Gestiona CRUD de libros
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/../models/Autor.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Calificacion.php';

class LibroController {

    private Libro $libroModel;
    private Autor $autorModel;
    private Categoria $categoriaModel;

    public function __construct() {
        Session::start();
        $this->libroModel = new Libro();
        $this->autorModel = new Autor();
        $this->categoriaModel = new Categoria();
    }

    // ─── LISTAR LIBROS ────────────────────────────────────────────────────────

    public function index(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        $libros = $this->libroModel->getAll();
        require_once __DIR__ . '/../views/libros/index.php';
    }

    // ─── CATÁLOGO PARA USUARIOS ───────────────────────────────────────────────

    public function catalogo(): void {
        Session::requireLogin();
        $libros = $this->libroModel->getAll();
        require_once __DIR__ . '/../views/libros/catalogo.php';
    }

    // ─── CALIFICAR LIBRO (solo rol usuario) ───────────────────────────────────

    public function calificar(): void {
        Session::requireRole(['usuario', 'bibliotecario']);
        Security::validateCSRF();

        $libroId   = Security::sanitizeInt($_POST['libro_id']   ?? 0);
        $estrellas = Security::sanitizeInt($_POST['estrellas']  ?? 0);
        $usuarioId = (int)Session::get('user_id');

        if ($libroId <= 0 || $estrellas < 1 || $estrellas > 5) {
            $_SESSION['cal_error'] = 'Calificación inválida. Selecciona entre 1 y 5 estrellas.';
            header('Location: index.php?page=catalogo-libros');
            exit();
        }

        $calModel = new Calificacion();
        $calModel->rate($usuarioId, $libroId, $estrellas);

        $_SESSION['cal_success'] = 'Tu calificación fue guardada exitosamente.';
        header('Location: index.php?page=catalogo-libros');
        exit();
    }

    // ─── CREAR LIBRO ──────────────────────────────────────────────────────────

    public function showCreate(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        $autores = $this->autorModel->getAll();
        $categorias = $this->categoriaModel->getAll();
        require_once __DIR__ . '/../views/libros/create.php';
    }

    public function processCreate(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        Security::validateCSRF();

        $titulo      = Security::sanitizeString($_POST['titulo'] ?? '');
        $autorInput  = Security::sanitizeString($_POST['autor'] ?? '');
        $categoriaId = Security::sanitizeInt($_POST['categoria_id'] ?? 0);
        $isbn        = Security::sanitizeString($_POST['isbn'] ?? '');
        $cantidad    = Security::sanitizeInt($_POST['cantidad'] ?? 0);
        $descripcion = Security::sanitizeString($_POST['descripcion'] ?? '');

        $errors = [];

        if (strlen($titulo) < 3) $errors[] = 'Título debe tener al menos 3 caracteres.';
        if (strlen($autorInput) < 3) $errors[] = 'El autor debe tener al menos 3 caracteres.';
        if ($categoriaId <= 0) $errors[] = 'Categoría es requerida.';
        if (!self::isValidISBN($isbn)) $errors[] = 'ISBN inválido. Debe tener 10 o 13 dígitos (guiones opcionales).';
        if ($cantidad <= 0) $errors[] = 'La cantidad debe ser mayor a 0.';
        if ($this->libroModel->isbnExists($isbn)) $errors[] = 'ISBN ya existe.';

        if (!empty($errors)) {
            $_SESSION['libro_errors'] = $errors;
            $_SESSION['libro_data'] = compact('titulo', 'autorInput', 'categoriaId', 'isbn', 'cantidad', 'descripcion');
            header('Location: index.php?page=libros&action=create');
            exit();
        }

        $existingAutor = $this->autorModel->getByName($autorInput);
        if ($existingAutor) {
            $autorId = (int)$existingAutor['id'];
        } else {
            $createdAuthor = $this->autorModel->create([
                'nombre' => $autorInput,
                'nacionalidad' => ''
            ]);

            if (!$createdAuthor) {
                $_SESSION['libro_errors'] = ['Error al crear el autor nuevo.'];
                $_SESSION['libro_data'] = compact('titulo', 'autorInput', 'categoriaId', 'isbn', 'cantidad', 'descripcion');
                header('Location: index.php?page=libros&action=create');
                exit();
            }

            $newAuthor = $this->autorModel->getByName($autorInput);
            $autorId = $newAuthor ? (int)$newAuthor['id'] : 0;
        }

        $created = $this->libroModel->create([
            'titulo'       => $titulo,
            'autor_id'     => $autorId,
            'categoria_id' => $categoriaId,
            'isbn'         => $isbn,
            'cantidad'     => $cantidad,
            'descripcion'  => $descripcion,
        ]);

        if ($created) {
            $_SESSION['success'] = 'Libro agregado exitosamente.';
            header('Location: index.php?page=libros');
        } else {
            $_SESSION['error'] = 'Error al crear el libro.';
            $_SESSION['libro_data'] = compact('titulo', 'autorInput', 'categoriaId', 'isbn', 'cantidad', 'descripcion');
            header('Location: index.php?page=libros&action=create');
        }
        exit();
    }

    // ─── EDITAR LIBRO ─────────────────────────────────────────────────────────

    public function showEdit(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        
        $id = Security::sanitizeInt($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Libro no especificado.';
            header('Location: index.php?page=libros');
            exit();
        }

        $libro = $this->libroModel->getById($id);
        if (!$libro) {
            $_SESSION['error'] = 'Libro no encontrado.';
            header('Location: index.php?page=libros');
            exit();
        }

        $autores = $this->autorModel->getAll();
        $categorias = $this->categoriaModel->getAll();
        require_once __DIR__ . '/../views/libros/edit.php';
    }

    public function processEdit(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        Security::validateCSRF();

        $id          = Security::sanitizeInt($_POST['id'] ?? 0);
        $titulo      = Security::sanitizeString($_POST['titulo'] ?? '');
        $autorId     = Security::sanitizeInt($_POST['autor_id'] ?? 0);
        $categoriaId = Security::sanitizeInt($_POST['categoria_id'] ?? 0);
        $isbn        = Security::sanitizeString($_POST['isbn'] ?? '');
        $cantidad    = Security::sanitizeInt($_POST['cantidad'] ?? 0);
        $estado      = Security::sanitizeString($_POST['estado'] ?? 'disponible');
        $descripcion = Security::sanitizeString($_POST['descripcion'] ?? '');

        $errors = [];

        if (!$id || !$this->libroModel->getById($id)) $errors[] = 'Libro no encontrado.';
        if (strlen($titulo) < 3) $errors[] = 'Título debe tener al menos 3 caracteres.';
        if ($autorId <= 0) $errors[] = 'Autor es requerido.';
        if ($categoriaId <= 0) $errors[] = 'Categoría es requerida.';
        if (!self::isValidISBN($isbn)) $errors[] = 'ISBN inválido. Debe tener 10 o 13 dígitos (guiones opcionales).';
        if ($cantidad < 0) $errors[] = 'Cantidad no puede ser negativa.';
        if ($this->libroModel->isbnExists($isbn, $id)) $errors[] = 'ISBN ya existe en otro libro.';

        if (!empty($errors)) {
            $_SESSION['libro_errors'] = $errors;
            header("Location: index.php?page=libros&action=edit&id={$id}");
            exit();
        }

        $updated = $this->libroModel->update($id, [
            'titulo'       => $titulo,
            'autor_id'     => $autorId,
            'categoria_id' => $categoriaId,
            'isbn'         => $isbn,
            'cantidad'     => $cantidad,
            'estado'       => $cantidad <= 0 ? 'agotado' : $estado,
            'descripcion'  => $descripcion,
        ]);

        if ($updated) {
            $_SESSION['success'] = 'Libro actualizado correctamente.';
            header('Location: index.php?page=libros');
        } else {
            $_SESSION['error'] = 'Error al actualizar el libro.';
            header("Location: index.php?page=libros&action=edit&id={$id}");
        }
        exit();
    }

    // ─── ELIMINAR LIBRO ───────────────────────────────────────────────────────

    public function delete(): void {
        Session::requireRole(['admin']);
        Security::validateCSRF();

        $id = Security::sanitizeInt($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Libro no especificado.';
            header('Location: index.php?page=libros');
            exit();
        }

        $libro = $this->libroModel->getById($id);
        if (!$libro) {
            $_SESSION['error'] = 'Libro no encontrado.';
            header('Location: index.php?page=libros');
            exit();
        }

        $deleted = $this->libroModel->delete($id);

        if ($deleted) {
            $_SESSION['success'] = 'Libro eliminado correctamente.';
        } else {
            $_SESSION['error'] = 'Error al eliminar el libro.';
        }

        header('Location: index.php?page=libros');
        exit();
    }

    // ─── BUSCAR LIBROS ────────────────────────────────────────────────────────

    public function search(): void {
        Session::requireRole(['admin', 'bibliotecario']);
        
        $query = Security::sanitizeString($_GET['q'] ?? '');
        $libros = !empty($query) ? $this->libroModel->search($query) : [];
        
        require_once __DIR__ . '/../views/libros/search.php';
    }

    // ─── HELPERS ──────────────────────────────────────────────────────────────

    /**
     * Valida formato ISBN-10 o ISBN-13 (ignora guiones y espacios).
     */
    private static function isValidISBN(string $isbn): bool {
        $clean = preg_replace('/[\s\-]/', '', $isbn);
        if (strlen($clean) === 10) {
            return (bool) preg_match('/^[0-9]{9}[0-9X]$/i', $clean);
        }
        if (strlen($clean) === 13) {
            return (bool) preg_match('/^97[89][0-9]{10}$/', $clean);
        }
        return false;
    }
}
