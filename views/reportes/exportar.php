<?php
/**
 * Vista de exportación/impresión — Reporte Mensual
 * Se abre en una nueva pestaña y activa el diálogo de impresión automáticamente.
 * El usuario puede guardar como PDF desde el diálogo de impresión del navegador.
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/ReporteController.php';

Session::start();
Session::requireRole(['admin']);

$meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
          'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

$d    = $reporte['datos'];
$anio = (int) $reporte['anio'];
$mes  = (int) $reporte['mes'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reporte <?= $meses[$mes] ?> <?= $anio ?> — Biblioteca Fusalmo</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
body { background:#fff; color:#222; font-size:13px; padding:30px 40px; }

/* Cabecera del PDF */
.pdf-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-bottom: 3px solid #1E3A6E;
    padding-bottom: 16px;
    margin-bottom: 24px;
}
.pdf-logo { font-size: 20px; font-weight: 900; color: #1E3A6E; }
.pdf-logo span { font-size: 11px; display: block; color: #8a95a3; font-weight: 400; margin-top: 3px; }
.pdf-period { text-align: right; }
.pdf-period-title { font-size: 18px; font-weight: 800; color: #1E3A6E; }
.pdf-period-meta  { font-size: 11px; color: #8a95a3; margin-top: 3px; }

/* KPI grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
.kpi-card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    border-left: 4px solid #1A4FA0;
    padding: 14px 12px;
}
.kpi-card--blue   { border-left-color: #1A4FA0; }
.kpi-card--red    { border-left-color: #dc3545; }
.kpi-card--orange { border-left-color: #fd7e14; }
.kpi-card--green  { border-left-color: #28a745; }
.kpi-card--teal   { border-left-color: #17a2b8; }
.kpi-label { font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #8a95a3; font-weight: 700; }
.kpi-value { font-size: 22px; font-weight: 800; color: #1E3A6E; line-height: 1.2; margin-top: 4px; }
.kpi-sub   { font-size: 9px; color: #bbb; margin-top: 2px; }

/* Sección */
.section { margin-bottom: 22px; }
.section-title {
    font-size: 11px;
    font-weight: 800;
    color: #1E3A6E;
    text-transform: uppercase;
    letter-spacing: .07em;
    border-left: 3px solid #1A4FA0;
    padding-left: 8px;
    margin-bottom: 10px;
}

/* Tablas */
table { width: 100%; border-collapse: collapse; font-size: 12px; }
table th { background: #1E3A6E; color: white; padding: 8px 10px; text-align: left; font-weight: 700; font-size: 11px; }
table td { padding: 7px 10px; border-bottom: 1px solid #eee; }
table tr:last-child td { border-bottom: none; }

/* Barra de progreso (texto para PDF) */
.bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 7px; }
.bar-label { width: 100px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
.bar-track { flex: 1; height: 7px; background: #f0f0f0; border-radius: 4px; overflow: hidden; }
.bar-fill  { height: 100%; border-radius: 4px; }
.bar-count { width: 60px; font-size: 11px; color: #555; text-align: right; }

/* Layout de dos columnas */
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 22px; }

/* Pie de página */
.pdf-footer {
    border-top: 1px solid #ddd;
    margin-top: 30px;
    padding-top: 10px;
    font-size: 10px;
    color: #aaa;
    display: flex;
    justify-content: space-between;
}

/* Botón de impresión (no se imprime) */
.print-btn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    background: #1A4FA0;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 4px 18px rgba(26,79,160,.4);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 999;
}

@media print {
    .print-btn { display: none !important; }
    body { padding: 10mm 15mm; }
    .kpi-card { border: 1px solid #ccc !important; }
}
</style>
</head>
<body>

<!-- Botón flotante de impresión -->
<button class="print-btn" onclick="window.print()">
    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2V11a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Imprimir / Guardar PDF
</button>

<!-- Cabecera -->
<div class="pdf-header">
    <div class="pdf-logo">
        Biblioteca Fusalmo
        <span>Sistema de Gestión · DSS 404 G03T</span>
    </div>
    <div class="pdf-period">
        <div class="pdf-period-title">Reporte Mensual · <?= $meses[$mes] ?> <?= $anio ?></div>
        <div class="pdf-period-meta">
            Generado el <?= date('d/m/Y \a \l\a\s H:i', strtotime($reporte['created_at'])) ?>
            <?php if (!empty($reporte['generado_por_nombre'])): ?>
                · por <?= htmlspecialchars($reporte['generado_por_nombre']) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card kpi-card--blue">
        <div class="kpi-label">Total Préstamos</div>
        <div class="kpi-value"><?= number_format($d['total_prestamos']) ?></div>
        <div class="kpi-sub">del mes</div>
    </div>
    <div class="kpi-card kpi-card--red">
        <div class="kpi-label">Vencidos</div>
        <div class="kpi-value"><?= number_format($d['prestamos_vencidos']) ?></div>
        <div class="kpi-sub">con estado vencido</div>
    </div>
    <div class="kpi-card kpi-card--orange">
        <div class="kpi-label">Multas</div>
        <div class="kpi-value">$<?= number_format($d['total_multas'] ?? 0, 0, ',', '.') ?></div>
        <div class="kpi-sub">monto acumulado</div>
    </div>
    <div class="kpi-card kpi-card--green">
        <div class="kpi-label">Devoluciones</div>
        <div class="kpi-value"><?= number_format($d['devoluciones']) ?></div>
        <div class="kpi-sub">libros devueltos</div>
    </div>
    <div class="kpi-card kpi-card--teal">
        <div class="kpi-label">Nuevos Usuarios</div>
        <div class="kpi-value"><?= number_format($d['nuevos_usuarios']) ?></div>
        <div class="kpi-sub">registrados</div>
    </div>
</div>

<!-- Dos columnas: Top libros + Categorías -->
<div class="two-col">

    <!-- Top 5 libros -->
    <div class="section">
        <div class="section-title">Top 5 Libros Más Prestados</div>
        <?php if (empty($d['libros_top5'])): ?>
            <p style="color:#aaa;font-size:11px">Sin préstamos en este período.</p>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Título</th><th>Autor</th><th style="text-align:right">Préstamos</th></tr></thead>
            <tbody>
            <?php foreach ($d['libros_top5'] as $i => $libro): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($libro['titulo']) ?></td>
                    <td><?= htmlspecialchars($libro['autor']) ?></td>
                    <td style="text-align:right;font-weight:700"><?= $libro['total_prestamos'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Categorías más activas -->
    <div class="section">
        <div class="section-title">Categorías Más Activas</div>
        <?php if (empty($d['categorias_activas'])): ?>
            <p style="color:#aaa;font-size:11px">Sin actividad en este período.</p>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Categoría</th><th style="text-align:right">Préstamos</th></tr></thead>
            <tbody>
            <?php foreach ($d['categorias_activas'] as $i => $cat): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($cat['nombre']) ?></td>
                    <td style="text-align:right;font-weight:700"><?= $cat['total_prestamos'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<!-- Inventario por estado -->
<div class="section">
    <div class="section-title">Inventario de Libros por Estado (Snapshot)</div>
    <?php if (empty($d['libros_por_estado'])): ?>
        <p style="color:#aaa;font-size:11px">Sin datos de inventario.</p>
    <?php else: ?>
    <?php
        $estadoColores = [
            'disponible' => '#28a745','prestado' => '#fd7e14',
            'deteriorado'=> '#ffc107','perdido'  => '#dc3545','agotado' => '#6c757d',
        ];
        $totalLibros = array_sum(array_column($d['libros_por_estado'], 'total'));
    ?>
    <?php foreach ($d['libros_por_estado'] as $item): ?>
        <?php
            $pct   = $totalLibros > 0 ? round($item['total'] / $totalLibros * 100) : 0;
            $color = $estadoColores[$item['estado']] ?? '#1A4FA0';
        ?>
        <div class="bar-row">
            <div class="bar-label"><?= htmlspecialchars($item['estado']) ?></div>
            <div class="bar-track">
                <div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
            </div>
            <div class="bar-count"><?= $item['total'] ?> (<?= $pct ?>%)</div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pie de página -->
<div class="pdf-footer">
    <span>Biblioteca Fusalmo · Sistema de Gestión DSS 404 G03T</span>
    <span>Impreso el <?= date('d/m/Y H:i') ?></span>
</div>

<script>
// Auto-abrir diálogo de impresión al cargar la página
window.addEventListener('load', function() {
    setTimeout(function(){ window.print(); }, 600);
});
</script>

</body>
</html>
