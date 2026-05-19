<?php require_once __DIR__.'/../layouts/header.php'; ?>

<style>
.badge-atraso {
    background: #dc3545;
    color: white;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: bold;
}
.badge-ok {
    background: #28a745;
    color: white;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: bold;
}
.badge-hoy {
    background: #ffc107;
    color: black;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: bold;
}
.alert-multa {
    background: #f8d7da;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 5px solid #dc3545;
    font-weight: bold;
}
</style>

<div class="card">

<h1><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="13" y2="16"></line></svg>Mis Préstamos Activos</h1>

<?php if (!empty($_SESSION['cal_success'])): ?>
<div style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:10px;margin-bottom:14px;border-left:4px solid #28a745;">
    <?= Security::escape($_SESSION['cal_success']) ?>
</div>
<?php unset($_SESSION['cal_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['cal_error'])): ?>
<div class="alert-multa">
    <?= Security::escape($_SESSION['cal_error']) ?>
</div>
<?php unset($_SESSION['cal_error']); ?>
<?php endif; ?>

<?php if ($multas > 0): ?>
<div class="alert-multa">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Tienes una multa pendiente de $<?= number_format($multas, 0) ?>
</div>
<?php endif; ?>

<table>

<thead>
<tr>
<th>Libro</th>
<th>Fecha Préstamo</th>
<th>Devolución Esperada</th>
<th>Días Restantes</th>
<th>Estado</th>
<th>Tu Calificación</th>
</tr>
</thead>

<tbody>

<?php if (empty($prestamos)): ?>
<tr>
<td colspan="6" style="text-align:center;color:#888;padding:30px;">
    ℹ️ No tienes préstamos activos
</td>
</tr>
<?php else: ?>

<?php foreach ($prestamos as $p): ?>
<tr>

<td><strong><?= Security::escape($p['titulo']) ?></strong></td>

<td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>

<td><?= date('d/m/Y', strtotime($p['fecha_devolucion_esperada'])) ?></td>

<td>
    <?php if ($p['dias_atraso'] > 0): ?>
        <span class="badge-atraso"><?= $p['dias_atraso'] ?> días de atraso</span>
    <?php elseif ($p['dias_atraso'] < 0): ?>
        <span class="badge-ok"><?= abs($p['dias_atraso']) ?> días restantes</span>
    <?php else: ?>
        <span class="badge-hoy">Vence hoy</span>
    <?php endif; ?>
</td>

<td>
    <?php if ($p['multa'] > 0): ?>
        <span class="badge badge-vencido">Multa: $<?= number_format($p['multa'], 0) ?></span>
    <?php else: ?>
        <span class="badge badge-activo">Activo</span>
    <?php endif; ?>
</td>

<td style="min-width:150px;">
<?php $miVoto = $misCalificaciones[$p['libro_id']] ?? null; ?>
<form method="POST"
      action="index.php?page=mis-prestamos&action=calificar"
      class="star-form"
      title="<?= $miVoto ? 'Tu calificación actual: '.$miVoto.' ★. Haz clic para cambiarla.' : 'Califica este libro' ?>">
    <?= Security::csrfField() ?>
    <input type="hidden" name="libro_id" value="<?= (int)$p['libro_id'] ?>">
    <div class="star-rating">
        <?php for ($v = 5; $v >= 1; $v--): ?>
        <input type="radio"
               name="estrellas"
               id="pr-s<?= $v ?>-<?= (int)$p['id'] ?>"
               value="<?= $v ?>"
               <?= ($miVoto == $v) ? 'checked' : '' ?>>
        <label for="pr-s<?= $v ?>-<?= (int)$p['id'] ?>">&#9733;</label>
        <?php endfor; ?>
    </div>
    <button type="submit" class="btn-rate">
        <?= $miVoto ? 'Actualizar' : 'Calificar' ?>
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
