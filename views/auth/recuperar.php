<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Recuperar contraseña</title>

<link rel="stylesheet"
href="/BibliotecaMVC/assets/css/estilos.css">

<style>

.auth-container{

display:flex;

justify-content:center;

align-items:center;

min-height:100vh;

padding:16px;
}

.auth-card{

width:100%;

max-width:420px;

background:white;

padding:40px;

border-radius:20px;

box-shadow:
0 5px 20px rgba(0,0,0,.1);

box-sizing:border-box;
}

.logo-circle{

width:80px;
height:80px;

margin:auto;

border-radius:50%;

background:#1E3A6E;

color:white;

display:flex;

align-items:center;

justify-content:center;

font-size:35px;

font-weight:bold;

margin-bottom:20px;
}

.text-center{

text-align:center;
}

</style>

</head>

<body>

<div class="auth-container">

<div class="auth-card">

<div class="logo-circle">

F

</div>

<div class="text-center">

<h2>Biblioteca Fusalmo</h2>

<p>Recuperar contraseña</p>

</div>

<br>

<?php if(!empty($_SESSION['auth_errors'])): ?>

<div class="alert alert-danger">

<?= Security::escape(
$_SESSION['auth_errors'][0]
) ?>

</div>

<?php unset($_SESSION['auth_errors']); ?>

<?php endif; ?>


<form
method="POST"
action="index.php?page=recuperar&action=process">

<?= Security::csrfField() ?>

<label>

Correo electrónico

</label>

<input
type="email"
name="correo"
placeholder="ej. usuario@correo.com"
required>

<br>

<button
type="submit"
class="btn btn-primary"
style="width:100%;">

Recuperar contraseña

</button>

</form>

<br>

<div class="text-center">

<a href="index.php?page=login">

Volver al login

</a>

</div>

</div>

</div>

</body>
</html>