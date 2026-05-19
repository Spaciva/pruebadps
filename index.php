<?php
/**
 * index.php — Front Controller
 * Biblioteca Fusalmo — DSS 404 G03T
 * Punto de entrada único
 */

// ── Inicialización ───────────────────────────────────────────

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/session.php';

Session::start();
Security::setSecurityHeaders();

// ── Parámetros ───────────────────────────────────────────────

$page = Security::sanitizeString(
    $_GET['page'] ?? 'login'
);

$action = Security::sanitizeString(
    $_GET['action'] ?? ''
);

// ── Páginas públicas ─────────────────────────────────────────

$publicPages = [
    'login',
    'recuperar'
];

if (
    !in_array($page, $publicPages)
    && !Session::isLoggedIn()
) {

    header(
        'Location:index.php?page=login'
    );

    exit();
}

// ── Enrutamiento ─────────────────────────────────────────────

switch($page){

// ── LOGIN ────────────────────────────────────────────────────

case 'login':

    require_once __DIR__ .
    '/controllers/AuthController.php';

    $ctrl = new AuthController();

    if(
        $_SERVER['REQUEST_METHOD']==='POST'
        &&
        $action==='process'
    ){

        $ctrl->processLogin();

    }else{

        $ctrl->showLogin();
    }

break;


// ── LOGOUT ───────────────────────────────────────────────────

case 'logout':

    require_once __DIR__ .
    '/controllers/AuthController.php';

    $ctrl = new AuthController();

    $ctrl->logout();

break;


// ── REGISTRO ─────────────────────────────────────────────────

case 'register':

    require_once __DIR__ .
    '/controllers/AuthController.php';

    $ctrl = new AuthController();

    if(
        $_SERVER['REQUEST_METHOD']==='POST'
        &&
        $action==='process'
    ){

        $ctrl->processRegister();

    }else{

        $ctrl->showRegister();
    }

break;


// ── RECUPERAR CONTRASEÑA ─────────────────────────────────────

case 'recuperar':

    require_once __DIR__ .
    '/controllers/AuthController.php';

    $ctrl = new AuthController();

    if(
        $_SERVER['REQUEST_METHOD']==='POST'
        &&
        $action==='process'
    ){

        $ctrl->processRecovery();

    }else{

        $ctrl->showRecovery();
    }

break;


// ── DASHBOARD ────────────────────────────────────────────────

case 'dashboard':

    Session::requireLogin();

    require_once __DIR__ .
    '/views/dashboard/index.php';

break;


// ── LIBROS ───────────────────────────────────────────────────

case 'libros':

    Session::requireRole([
        'admin',
        'bibliotecario'
    ]);

    require_once __DIR__ .
    '/controllers/LibroController.php';

    $ctrl = new LibroController();

    if($_SERVER['REQUEST_METHOD']==='POST'){

        match($action){

            'process-create'
                => $ctrl->processCreate(),

            'process-edit'
                => $ctrl->processEdit(),

            'delete'
                => $ctrl->delete(),

            default
                => $ctrl->index()
        };

    }else{

        match($action){

            'create'
                => $ctrl->showCreate(),

            'edit'
                => $ctrl->showEdit(),

            'search'
                => $ctrl->search(),

            default
                => $ctrl->index()
        };
    }

break;

// ── CATEGORÍAS ───────────────────────────────────────────────

case 'categorias':

    Session::requireRole([
        'admin'
    ]);

    require_once __DIR__ .
    '/controllers/CategoriaController.php';

    $ctrl = new CategoriaController();

    if($_SERVER['REQUEST_METHOD']==='POST'){

        match($action){

            'process-create'
                => $ctrl->processCreate(),

            'process-edit'
                => $ctrl->processEdit(),

            'delete'
                => $ctrl->delete(),

            default
                => $ctrl->index()
        };

    }else{

        match($action){

            'create'
                => $ctrl->showCreate(),

            'edit'
                => $ctrl->showEdit(),

            default
                => $ctrl->index()
        };
    }

break;

// ── AUTORES ──────────────────────────────────────────────────

case 'autores':

    Session::requireRole([
        'admin',
        'bibliotecario'
    ]);

    require_once __DIR__ .
    '/controllers/AutorController.php';

    $ctrl=new AutorController();

    $ctrl->index();

break;


// ── PRÉSTAMOS ────────────────────────────────────────────────

case 'prestamos':

    Session::requireRole([
        'admin',
        'bibliotecario'
    ]);

    require_once __DIR__ .
    '/controllers/PrestamoController.php';

    $ctrl = new PrestamoController();

    if($_SERVER['REQUEST_METHOD']==='POST'){

        match($action){

            'process-create'
                => $ctrl->processCreate(),

            'devolucion'
                => $ctrl->procesarDevolucion(),

            'renovar'
                => $ctrl->renovar(),

            default
                => $ctrl->index()
        };

    }else{

        match($action){

            'create'
                => $ctrl->showCreate(),

            'vencidos'
                => $ctrl->vencidos(),

            'mis-prestamos'
                => $ctrl->misPrestamos(),

            default
                => $ctrl->index()
        };
    }

break;


// ── USUARIOS ─────────────────────────────────────────────────

// ── USUARIOS ─────────────────────────────────────────────────

case 'usuarios':

    Session::requireRole([
        'admin',
        'bibliotecario'
    ]);

    require_once __DIR__ .
    '/controllers/UsuarioController.php';

    $ctrl = new UsuarioController();

    if($_SERVER['REQUEST_METHOD']==='POST'){

        match($action){

            'process-edit'
                => $ctrl->processEdit(),

            'delete'
                => $ctrl->delete(),

            'process-change-password'
                => $ctrl->processChangePassword(),

            'toggle-status'
                => $ctrl->toggleStatus(),

            default
                => $ctrl->index()
        };

    }else{

        match($action){

            'edit'
                => $ctrl->showEdit(),

            'change-password'
                => $ctrl->showChangePassword(),

            default
                => $ctrl->index()
        };

    }

break;


// ── CATÁLOGO DE LIBROS (USUARIO) ─────────────────────────────

case 'catalogo-libros':

    Session::requireLogin();

    require_once __DIR__ .
    '/controllers/LibroController.php';

    $ctrl = new LibroController();

    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && $action === 'calificar'
    ) {
        $ctrl->calificar();
    } else {
        $ctrl->catalogo();
    }

break;


// ── MIS PRÉSTAMOS (USUARIO) ──────────────────────────────────

case 'mis-prestamos':

    Session::requireLogin();

    require_once __DIR__ .
    '/controllers/PrestamoController.php';

    $ctrl = new PrestamoController();

    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && $action === 'calificar'
    ) {
        $ctrl->calificarLibro();
    } else {
        $ctrl->misPrestamos();
    }

break;


// ── REPORTES (solo admin) ─────────────────────────────────────

case 'reportes':

    Session::requireRole(['admin']);

    require_once __DIR__ .
    '/controllers/ReporteController.php';

    $ctrl = new ReporteController();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        match($action) {
            'guardar' => $ctrl->guardar(),
            default   => $ctrl->index()
        };

    } else {

        match($action) {
            'ver'      => $ctrl->ver(),
            'exportar' => $ctrl->exportar(),
            default    => $ctrl->index()
        };

    }

break;


// ── ERROR 404 ────────────────────────────────────────────────

default:

    http_response_code(404);

    echo '
    <h1>Página no encontrada</h1>

    <a href="index.php?page=dashboard">

    Volver al inicio

    </a>
    ';
}