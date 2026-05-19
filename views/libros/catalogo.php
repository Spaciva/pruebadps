<?php require_once __DIR__.'/../layouts/header.php'; ?>



<div class="card">

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
<h1><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>Catálogo de Libros</h1>
</div>

<!-- MENSAJES -->

<?php if (!empty($_SESSION['cal_success'])): ?>
<div class="alert alert-success">
    <?= Security::escape($_SESSION['cal_success']) ?>
</div>
<?php unset($_SESSION['cal_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['cal_error'])): ?>
<div class="alert alert-danger">
    <?= Security::escape($_SESSION['cal_error']) ?>
</div>
<?php unset($_SESSION['cal_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success">
    <?= Security::escape($_SESSION['success']) ?>
</div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>



<!-- TABLA -->

<table>

<thead>
<tr>
<th>ID</th>
<th>Título</th>
<th>Autor</th>
<th>Categoría</th>
<th>ISBN</th>
<th>Disponibles</th>
<th>Estado</th>
<th>Calificación</th>
</tr>
</thead>

<tbody>

<?php if (empty($libros)): ?>
<tr>
<td colspan="8">No hay libros registrados</td>
</tr>
<?php else: ?>

<?php foreach ($libros as $libro): ?>
<?php
    $promedio = $libro['promedio_calificacion'] ?? null;
    $total    = (int)($libro['total_calificaciones'] ?? 0);
?>
<tr>
<td><?= (int)$libro['id'] ?></td>

<td><strong><?= Security::escape($libro['titulo']) ?></strong></td>

<td><?= Security::escape($libro['autor'] ?? '-') ?></td>

<td><?= Security::escape($libro['categoria'] ?? '-') ?></td>

<td><?= Security::escape($libro['isbn']) ?></td>

<td><?= (int)$libro['cantidad'] ?></td>

<td>
<span class="badge badge-<?= $libro['estado'] ?>">
<?= ucfirst($libro['estado']) ?>
</span>
</td>

<td style="min-width:160px;">
<!-- Promedio visible para todos -->
<div class="stars-display" title="<?= $promedio ? $promedio . ' de 5' : 'Sin calificaciones' ?>">
<?php
    $rounded = $promedio ? (int)round((float)$promedio) : 0;
    for ($i = 1; $i <= 5; $i++):
?>
<span class="star <?= $i <= $rounded ? 'star-filled' : 'star-empty' ?>">&#9733;</span>
<?php endfor; ?>
</div>
<div class="stars-meta">
<?php if ($promedio): ?>
    <?= number_format((float)$promedio, 1) ?> / 5
    <span style="color:#aaa;">(<?= $total ?>)</span>
<?php else: ?>
    <span style="color:#aaa;">Sin calificaciones</span>
<?php endif; ?>
</div>



</td>
</tr>
<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>

