<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Biblioteca Fusalmo</title>

<link rel="stylesheet"
href="/BibliotecaMVC/assets/css/estilos.css">

</head>

<body>

<div class="navbar">

<a href="index.php?page=dashboard" class="logo" style="text-decoration:none; color:white;">
    Biblioteca Fusalmo
</a>

<button class="nav-toggle" id="navToggle" aria-label="Abrir menú" aria-expanded="false">
    <span></span>
    <span></span>
    <span></span>
</button>

<div class="nav-links" id="navLinks">

<?php $userRole = Session::get('user_role') ?? ''; ?>

<a href="index.php?page=dashboard">
Inicio
</a>

<?php if ($userRole === 'admin' || $userRole === 'bibliotecario'): ?>
<a href="index.php?page=libros">
Libros
</a>
<?php endif; ?>

<?php if ($userRole === 'usuario'): ?>
<a href="index.php?page=catalogo-libros">
Libros
</a>
<?php endif; ?>

<?php if ($userRole === 'admin'): ?>
<a href="index.php?page=categorias">
Categorías
</a>
<?php endif; ?>

<?php if ($userRole === 'admin' || $userRole === 'bibliotecario'): ?>
<a href="index.php?page=prestamos">
Préstamos
</a>
<?php endif; ?>

<?php if ($userRole === 'usuario' || $userRole === 'bibliotecario'): ?>
<a href="index.php?page=mis-prestamos">
Mis Préstamos
</a>
<?php endif; ?>

<?php if ($userRole === 'admin' || $userRole === 'bibliotecario'): ?>
<a href="index.php?page=usuarios">
Usuarios
</a>
<?php endif; ?>

<?php if ($userRole === 'admin'): ?>
<a href="index.php?page=reportes">
Reportes
</a>
<?php endif; ?>

<a href="index.php?page=logout">
Cerrar sesión
</a>

</div>

</div>

<div class="container">

<?php if (($_GET['page'] ?? 'dashboard') !== 'dashboard'): ?>
<a href="index.php?page=dashboard" class="back-btn" title="Volver al inicio">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
</a>
<?php endif; ?>

<script>
(function(){
    var toggle = document.getElementById('navToggle');
    var links  = document.getElementById('navLinks');
    if (!toggle || !links) return;
    toggle.addEventListener('click', function(){
        var open = links.classList.toggle('open');
        toggle.classList.toggle('open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
    });
    links.querySelectorAll('a').forEach(function(a){
        a.addEventListener('click', function(){
            links.classList.remove('open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
})();
</script>