<?php require_once __DIR__.'/../layouts/header.php'; ?>

<style>

.badge-activo{
background:#28a745;
}

.badge-inactivo{
background:#dc3545;
}

.badge-admin{
background:#007bff;
}

.badge-bibliotecario{
background:#17a2b8;
}

.badge-usuario{
background:#6c757d;
}

.badge{

padding:6px 10px;

border-radius:8px;

color:white;
}

</style>


<div class="card">

<div
style="
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;">

<h1>👥 Gestión de Usuarios</h1>

<a
href="index.php?page=register"
class="btn btn-primary">

Agregar Usuario

</a>

</div>


<!-- MENSAJES -->

<?php if(!empty($_SESSION['success'])): ?>

<div class="alert alert-success">

<?= Security::escape(
$_SESSION['success']
) ?>

</div>

<?php unset($_SESSION['success']); ?>

<?php endif; ?>


<?php if(!empty($_SESSION['error'])): ?>

<div class="alert alert-danger">

<?= Security::escape(
$_SESSION['error']
) ?>

</div>

<?php unset($_SESSION['error']); ?>

<?php endif; ?>


<!-- TABLA -->

<table>

<thead>

<tr>

<th>ID</th>
<th>Nombre</th>
<th>Correo</th>
<th>Teléfono</th>
<th>Rol</th>
<th>Estado</th>
<th>Registrado</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php if(empty($usuarios)): ?>

<tr>

<td colspan="8">

No existen usuarios registrados

</td>

</tr>

<?php else: ?>

<?php foreach($usuarios as $usuario): ?>

<tr>

<td>

<?= Security::escape(
$usuario['id']
) ?>

</td>

<td>

<strong>

<?= Security::escape(
$usuario['nombre']
) ?>

</strong>

</td>

<td>

<?= Security::escape(
$usuario['correo']
) ?>

</td>

<td>

<?= Security::escape(
$usuario['telefono'] ?? '-'
) ?>

</td>

<td>

<span
class="badge badge-<?= $usuario['rol'] ?>">

<?= ucfirst(
$usuario['rol']
) ?>

</span>

</td>

<td>

<span
class="badge badge-<?= $usuario['estado'] ?>">

<?= ucfirst(
$usuario['estado']
) ?>

</span>

</td>

<td>

<?= date(
'd/m/Y',
strtotime(
$usuario['created_at']
)
) ?>

</td>

<td>

<a
href="index.php?page=usuarios&action=edit&id=<?= $usuario['id'] ?>"
class="btn btn-warning">

Editar

</a>


<a
href="index.php?page=usuarios&action=change-password&id=<?= $usuario['id'] ?>"
class="btn btn-success">

Contraseña

</a>


<?php if(
$usuario['id']
!=
$_SESSION['user_id']
): ?>

<form
method="POST"
action="index.php?page=usuarios&action=delete"
style="display:inline;">

<?= Security::csrfField() ?>

<input
type="hidden"
name="id"
value="<?= $usuario['id'] ?>">

<button
type="submit"
class="btn btn-danger">

Eliminar

</button>

</form>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>