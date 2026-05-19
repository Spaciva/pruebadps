<?php require_once __DIR__.'/../layouts/header.php'; ?>




<div class="card">

<div
style="
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;">

<h1><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>Gestión de Usuarios</h1>

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

<div style="overflow-x:auto;">
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

<div style="display:flex; gap:6px; align-items:center; justify-content:center;">

<a
href="index.php?page=usuarios&action=edit&id=<?= $usuario['id'] ?>"
class="btn-icon btn-warning"
title="Editar usuario">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
</a>

<?php if(($_SESSION['user_role'] ?? '') === 'admin'): ?>

<a
href="index.php?page=usuarios&action=change-password&id=<?= $usuario['id'] ?>"
class="btn-icon btn-success"
title="Cambiar contraseña">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
</a>

<?php if($usuario['id'] != $_SESSION['user_id']): ?>

<form
method="POST"
action="index.php?page=usuarios&action=toggle-status"
style="margin:0;"
onsubmit="return confirm('¿<?= $usuario['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?> al usuario <?= Security::escape($usuario['nombre']) ?>?')">
<?= Security::csrfField() ?>
<input type="hidden" name="id" value="<?= $usuario['id'] ?>">
<button
type="submit"
class="btn-icon <?= $usuario['estado'] === 'activo' ? 'btn-warning' : 'btn-success' ?>"
title="<?= $usuario['estado'] === 'activo' ? 'Desactivar usuario' : 'Activar usuario' ?>">
<?php if($usuario['estado'] === 'activo'): ?>
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
<?php else: ?>
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
<?php endif; ?>
</button>
</form>

<form
method="POST"
action="index.php?page=usuarios&action=delete"
style="margin:0;">

<?= Security::csrfField() ?>

<input
type="hidden"
name="id"
value="<?= $usuario['id'] ?>">

<button
type="submit"
class="btn-icon btn-danger"
title="Eliminar usuario"
onclick="return confirm('¿Eliminar este usuario?')">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
</button>

</form>

<?php else: ?>

<span style="width:34px; display:inline-block;"></span>

<?php endif; ?>

<?php endif; ?>

</div>

</td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>
</div>

</div>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>