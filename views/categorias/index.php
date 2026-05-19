<?php require_once __DIR__.'/../layouts/header.php'; ?>

<div class="card">

<div
style="
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;">

<h1><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>Gestión de Categorías</h1>

<a
href="index.php?page=categorias&action=create"
class="btn btn-primary">

Agregar Categoría

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


<!-- TABLA -->

<table>

<thead>

<tr>

<th>ID</th>
<th>Nombre</th>
<th>Descripción</th>
<th>Libros</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php if(empty($categorias)): ?>

<tr>

<td colspan="5">

No existen categorías registradas

</td>

</tr>

<?php else: ?>

<?php foreach($categorias as $cat): ?>

<tr>

<td>

<?= $cat['id'] ?>

</td>

<td>

<strong>

<?= Security::escape(
$cat['nombre']
) ?>

</strong>

</td>

<td>

<?= Security::escape(
$cat['descripcion']
?? '-'
) ?>

</td>

<td>

<?= $this->categoriaModel
->getLibrosCount(
$cat['id']
) ?>

</td>

<td>

<div style="display:flex; gap:6px; align-items:center; justify-content:center;">

<a
href="index.php?page=categorias&action=edit&id=<?= $cat['id'] ?>"
class="btn-icon btn-warning"
title="Editar categoría">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
</a>

<form
method="POST"
action="index.php?page=categorias&action=delete"
style="margin:0;"
onsubmit="return confirm('¿Eliminar categoría?');">

<?= Security::csrfField() ?>

<input
type="hidden"
name="id"
value="<?= $cat['id'] ?>">

<button
type="submit"
class="btn-icon btn-danger"
title="Eliminar categoría">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
</button>

</form>

</div>

</td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>