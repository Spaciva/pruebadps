<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../controllers/ReporteController.php'; ?>

<?php
$meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
          'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

$d    = $reporte['datos'];
$anio = (int) $reporte['anio'];
$mes  = (int) $reporte['mes'];
?>

<style>
.rep-title   { font-size: 1.5rem; font-weight: 800; color: #1E3A6E; }
.rep-badge   { display: inline-block; background: rgba(26,79,160,.1); color: #1A4FA0; padding: 5px 12px; border-radius: 20px; font-size: .8rem; font-weight: 700; }

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 16px;
    margin: 24px 0;
}
.kpi-card {
    background: white;
    border-radius: 14px;
    padding: 20px 18px;
    box-shadow: 0 3px 12px rgba(0,0,0,.07);
    border-left: 5px solid transparent;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.kpi-card--blue   { border-left-color: #1A4FA0; }
.kpi-card--red    { border-left-color: #dc3545; }
.kpi-card--orange { border-left-color: #fd7e14; }
.kpi-card--green  { border-left-color: #28a745; }
.kpi-card--teal   { border-left-color: #17a2b8; }
.kpi-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #8a95a3; font-weight: 600; }
.kpi-value { font-size: 1.8rem; font-weight: 800; color: #1E3A6E; line-height: 1; }
.kpi-sub   { font-size: .75rem; color: #bbb; }

.rep-section-title {
    font-size: .95rem;
    font-weight: 700;
    color: #1E3A6E;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rep-section-title::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 16px;
    background: #1A4FA0;
    border-radius: 3px;
}
</style>

<!-- Cabecera -->
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:26px">
    <div>
        <span class="rep-badge">Reporte guardado</span>
        <div class="rep-title" style="margin-top:8px">
            <?= $meses[$mes] ?> <?= $anio ?>
        </div>
        <div style="font-size:.85rem;color:#8a95a3;margin-top:4px">
            Generado el <?= date('d/m/Y \a \l\a\s H:i', strtotime($reporte['created_at'])) ?>
            <?php if (!empty($reporte['generado_por_nombre'])): ?>
                · por <?= htmlspecialchars($reporte['generado_por_nombre']) ?>
            <?php endif; ?>
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="index.php?page=reportes&action=exportar&id=<?= $reporte['id'] ?>"
           target="_blank" class="btn btn-primary" style="padding:10px 18px">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:5px"><polyline points="6 9 12 15 18 9"/><line x1="12" y1="3" x2="12" y2="15"/><rect x="3" y="17" width="18" height="4" rx="1"/></svg>
            Exportar PDF
        </a>
        <a href="index.php?page=reportes" class="btn" style="background:#6c757d;padding:10px 18px">
            ← Volver
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card kpi-card--blue">
        <span class="kpi-label">Total Préstamos</span>
        <span class="kpi-value"><?= number_format($d['total_prestamos']) ?></span>
        <span class="kpi-sub"><?= $meses[$mes] ?> <?= $anio ?></span>
    </div>
    <div class="kpi-card kpi-card--red">
        <span class="kpi-label">Préstamos Vencidos</span>
        <span class="kpi-value"><?= number_format($d['prestamos_vencidos']) ?></span>
        <span class="kpi-sub">Estado vencido</span>
    </div>
    <div class="kpi-card kpi-card--orange">
        <span class="kpi-label">Total en Multas</span>
        <span class="kpi-value">$<?= number_format($d['total_multas'] ?? 0, 0, ',', '.') ?></span>
        <span class="kpi-sub">Monto acumulado</span>
    </div>
    <div class="kpi-card kpi-card--green">
        <span class="kpi-label">Devoluciones</span>
        <span class="kpi-value"><?= number_format($d['devoluciones']) ?></span>
        <span class="kpi-sub">Libros devueltos</span>
    </div>
    <div class="kpi-card kpi-card--teal">
        <span class="kpi-label">Nuevos Usuarios</span>
        <span class="kpi-value"><?= number_format($d['nuevos_usuarios']) ?></span>
        <span class="kpi-sub">Registros del mes</span>
    </div>
</div>

<!-- Tablas de detalle -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:22px">

    <!-- Top 5 libros -->
    <div class="card">
        <div class="rep-section-title">Top 5 Libros Más Prestados</div>
        <?php if (empty($d['libros_top5'])): ?>
            <p style="color:#aaa;font-size:.85rem">Sin préstamos en este período.</p>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Título</th><th>Autor</th><th>Préstamos</th></tr></thead>
            <tbody>
            <?php foreach ($d['libros_top5'] as $i => $libro): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td style="text-align:left"><?= htmlspecialchars($libro['titulo']) ?></td>
                    <td style="text-align:left"><?= htmlspecialchars($libro['autor']) ?></td>
                    <td><strong><?= $libro['total_prestamos'] ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Libros por estado -->
    <div class="card">
        <div class="rep-section-title">Inventario de Libros por Estado <small style="color:#aaa;font-weight:400">(snapshot)</small></div>
        <?php if (empty($d['libros_por_estado'])): ?>
            <p style="color:#aaa;font-size:.85rem">Sin datos.</p>
        <?php else: ?>
        <?php
            $estadoColores = [
                'disponible' => '#28a745','prestado' => '#fd7e14',
                'deteriorado'=> '#ffc107','perdido'  => '#dc3545','agotado' => '#6c757d',
            ];
            $totalLibros = array_sum(array_column($d['libros_por_estado'], 'total'));
        ?>
        <div style="display:flex;flex-direction:column;gap:11px">
        <?php foreach ($d['libros_por_estado'] as $item): ?>
            <?php
                $pct   = $totalLibros > 0 ? round($item['total'] / $totalLibros * 100) : 0;
                $color = $estadoColores[$item['estado']] ?? '#1A4FA0';
            ?>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                    <span style="font-size:.83rem;font-weight:600;text-transform:capitalize"><?= htmlspecialchars($item['estado']) ?></span>
                    <span style="font-size:.83rem;color:#555"><?= $item['total'] ?> <small style="color:#aaa">(<?= $pct ?>%)</small></span>
                </div>
                <div style="height:7px;background:#f0f0f0;border-radius:6px;overflow:hidden">
                    <div style="height:100%;width:<?= $pct ?>%;background:<?= $color ?>;border-radius:6px"></div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Categorías más activas -->
    <div class="card">
        <div class="rep-section-title">Categorías Más Activas</div>
        <?php if (empty($d['categorias_activas'])): ?>
            <p style="color:#aaa;font-size:.85rem">Sin actividad en este período.</p>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Categoría</th><th>Préstamos</th></tr></thead>
            <tbody>
            <?php foreach ($d['categorias_activas'] as $i => $cat): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td style="text-align:left"><?= htmlspecialchars($cat['nombre']) ?></td>
                    <td><strong><?= $cat['total_prestamos'] ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
