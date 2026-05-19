<?php require_once __DIR__.'/../layouts/header.php'; ?>

<div class="card">

<div
style="
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;">

<h1><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>Préstamos Vencidos</h1>

<a
href="index.php?page=prestamos"
class="btn btn-secondary">

Volver

</a>

</div>


<div class="alert-vencidos">

<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Se aplicará multa de $30 USD por cada préstamo vencido al registrar devolución.

</div>


<table>

<thead>

<tr>

<th>ID</th>
<th>Usuario</th>
<th>Libro</th>
<th>Vencimiento</th>
<th>Atraso (días)</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php if(empty($vencidos)): ?>

<tr>

<td colspan="6"
style="
text-align:center;
padding:30px;">

<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> No hay préstamos vencidos

</td>

</tr>

<?php else: ?>

<?php foreach($vencidos as $v): ?>

<tr>

<td><?= $v['id'] ?></td>

<td>

<strong>

<?= Security::escape(
$v['usuario']
) ?>

</strong>

</td>

<td>

<?= Security::escape(
$v['libro']
) ?>

</td>

<td>

<?= date(
'd/m/Y',
strtotime(
$v['fecha_devolucion_esperada']
)
) ?>

</td>

<td>

<span class="badge-atraso">

<?= $v['dias_atraso'] ?>

días

</span>

</td>

<td>

<form
method="POST"
action="index.php?page=prestamos&action=devolucion"
style="margin:0;">

<?= Security::csrfField() ?>

<input
type="hidden"
name="id"
value="<?= $v['id'] ?>">

<button
type="submit"
class="btn-icon btn-success"
title="Registrar devolución">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
</button>

</form>

</td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>