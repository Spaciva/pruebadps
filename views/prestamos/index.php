<?php require_once __DIR__.'/../layouts/header.php'; ?>

<div class="card">

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">

<h1><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>Gestión de Préstamos</h1>

<div>

<a
href="index.php?page=prestamos&action=create"
class="btn btn-primary">

Nuevo Préstamo

</a>

<a
href="index.php?page=prestamos&action=vencidos"
class="btn btn-danger">

Vencidos

</a>

</div>

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


<!-- TABLA -->

<table>

<thead>

<tr>

<th>ID</th>
<th>Usuario</th>
<th>Libro</th>
<th>Fecha préstamo</th>
<th>Vencimiento</th>
<th>Estado</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php if(empty($prestamos)): ?>

<tr>

<td colspan="7">

No existen préstamos registrados

</td>

</tr>

<?php else: ?>

<?php foreach($prestamos as $p): ?>

<tr>

<td>

<?= $p['id'] ?>

</td>

<td>

<?= Security::escape(
$p['usuario']
) ?>

</td>

<td>

<?= Security::escape(
$p['libro']
) ?>

</td>

<td>

<?= date(
'd/m/Y',
strtotime(
$p['fecha_prestamo']
)
) ?>

</td>

<td>

<?= date(
'd/m/Y',
strtotime(
$p['fecha_devolucion_esperada']
)
) ?>

</td>

<td>

<span class="badge badge-<?= $p['estado'] ?>">
<?= ucfirst($p['estado']) ?>
</span>

</td>

<td>

<?php if(
$p['estado']==='activo'
): ?>

<div style="display:flex; gap:6px; align-items:center; justify-content:center;">

<form
method="POST"
action="index.php?page=prestamos&action=devolucion"
style="margin:0;">

<?= Security::csrfField() ?>

<input
type="hidden"
name="id"
value="<?= $p['id'] ?>">

<button
type="submit"
class="btn-icon btn-success"
title="Registrar devolución">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
</button>

</form>

<form
method="POST"
action="index.php?page=prestamos&action=renovar"
style="margin:0;">

<?= Security::csrfField() ?>

<input
type="hidden"
name="id"
value="<?= $p['id'] ?>">

<button
type="submit"
class="btn-icon btn-warning"
title="Renovar préstamo">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
</button>

</form>

</div>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>