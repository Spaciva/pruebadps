<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../controllers/ReporteController.php'; ?>

<?php
$meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
          'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$anioActual = (int) date('Y');
$mesActual  = (int) date('n');
$esMesActual = ($anio === $anioActual && $mes === $mesActual);

$d = $datos; // alias corto
?>

<style>
/* ── Cabecera de reportes ── */
.rep-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 28px;
}
.rep-title { font-size: 1.55rem; font-weight: 800; color: #1E3A6E; }
.rep-subtitle { font-size: .9rem; color: #8a95a3; margin-top: 3px; }

/* ── Selector de mes ── */
.rep-filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.rep-filter-form select,
.rep-filter-form input[type="number"] {
    width: auto;
    padding: 9px 12px;
    margin: 0;
    border-radius: 8px;
    border: 1px solid #d0d7e3;
    font-size: .9rem;
    background: white;
}

/* ── KPI Cards ── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 18px;
    margin-bottom: 30px;
}
.kpi-card {
    background: white;
    border-radius: 16px;
    padding: 22px 20px;
    box-shadow: 0 3px 14px rgba(0,0,0,.07);
    border-left: 5px solid transparent;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.kpi-card--blue   { border-left-color: #1A4FA0; }
.kpi-card--red    { border-left-color: #dc3545; }
.kpi-card--orange { border-left-color: #fd7e14; }
.kpi-card--green  { border-left-color: #28a745; }
.kpi-card--teal   { border-left-color: #17a2b8; }
.kpi-label  { font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; color: #8a95a3; font-weight: 600; }
.kpi-value  { font-size: 2rem; font-weight: 800; color: #1E3A6E; line-height: 1; }
.kpi-sub    { font-size: .78rem; color: #aaa; }

/* ── Secciones de tablas ── */
.rep-section { margin-bottom: 28px; }
.rep-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1E3A6E;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rep-section-title::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 18px;
    background: #1A4FA0;
    border-radius: 3px;
}

/* ── Historial ── */
.hist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}
.hist-card {
    background: white;
    border-radius: 14px;
    padding: 20px 18px;
    box-shadow: 0 3px 12px rgba(0,0,0,.07);
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-top: 3px solid #1A4FA0;
    transition: transform .15s, box-shadow .15s;
}
.hist-card:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(0,0,0,.12); }
.hist-month { font-size: 1.1rem; font-weight: 800; color: #1E3A6E; }
.hist-meta  { font-size: .78rem; color: #8a95a3; }
.hist-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }

/* ── Barra de estado guardado ── */
.status-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: .88rem;
    font-weight: 600;
}
.status-bar--saved    { background: rgba(40,167,69,.1);  color: #155724; border: 1px solid rgba(40,167,69,.3); }
.status-bar--unsaved  { background: rgba(255,193,7,.12); color: #856404; border: 1px solid rgba(255,193,7,.4); }

/* ── Responsive ── */
@media (max-width: 600px) {
    .rep-header { flex-direction: column; align-items: flex-start; }
    .kpi-value  { font-size: 1.6rem; }
}
</style>

<div class="rep-header">
    <div>
        <div class="rep-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" stroke="#1E3A6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>
            Reportes Mensuales
        </div>
        <div class="rep-subtitle">Estadísticas del sistema · Solo administrador</div>
    </div>

    <!-- Filtro de mes/año -->
    <form method="GET" action="index.php" class="rep-filter-form">
        <input type="hidden" name="page" value="reportes">
        <select name="mes">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $m === $mes ? 'selected' : '' ?>>
                    <?= $meses[$m] ?>
                </option>
            <?php endfor; ?>
        </select>
        <input type="number" name="anio" value="<?= htmlspecialchars($anio) ?>"
               min="2020" max="<?= $anioActual ?>" style="width:90px">
        <button type="submit" class="btn btn-primary" style="padding:9px 16px">Ver</button>
    </form>
</div>

<!-- Mensajes flash -->
<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert-success" style="background:rgba(40,167,69,.12);color:#155724;padding:12px 16px;border-radius:10px;margin-bottom:18px;border:1px solid rgba(40,167,69,.3)">
        <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert-danger">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Indicador de estado del reporte -->
<?php if ($yaGuardado): ?>
    <div class="status-bar status-bar--saved">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Reporte de <?= $meses[$mes] ?> <?= $anio ?> ya fue guardado.
        <a href="index.php?page=reportes&action=exportar&anio=<?= $anio ?>&mes=<?= $mes ?>"
           style="margin-left:auto;color:#155724;text-decoration:underline">Exportar PDF</a>
    </div>
<?php else: ?>
    <div class="status-bar status-bar--unsaved">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Los datos se muestran en vivo. Guarda el reporte para conservarlo en el historial.
    </div>
<?php endif; ?>

<!-- ── KPI Cards ─────────────────────────────────────────────────── -->
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
        <span class="kpi-value">$<?= number_format($d['total_multas'], 0, ',', '.') ?></span>
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

<!-- ── Tablas de detalle ──────────────────────────────────────────── -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:22px;margin-bottom:30px">

    <!-- Top 5 libros -->
    <div class="card">
        <div class="rep-section-title">Top 5 Libros Más Prestados</div>
        <?php if (empty($d['libros_top5'])): ?>
            <p style="color:#aaa;font-size:.88rem">Sin préstamos en este período.</p>
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
        <div class="rep-section-title">Inventario de Libros por Estado</div>
        <?php if (empty($d['libros_por_estado'])): ?>
            <p style="color:#aaa;font-size:.88rem">No hay libros registrados.</p>
        <?php else: ?>
        <?php
            $estadoColores = [
                'disponible' => '#28a745',
                'prestado'   => '#fd7e14',
                'deteriorado'=> '#ffc107',
                'perdido'    => '#dc3545',
                'agotado'    => '#6c757d',
            ];
            $totalLibros = array_sum(array_column($d['libros_por_estado'], 'total'));
        ?>
        <div style="display:flex;flex-direction:column;gap:12px">
        <?php foreach ($d['libros_por_estado'] as $item): ?>
            <?php
                $pct   = $totalLibros > 0 ? round($item['total'] / $totalLibros * 100) : 0;
                $color = $estadoColores[$item['estado']] ?? '#1A4FA0';
            ?>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                    <span style="font-size:.85rem;font-weight:600;text-transform:capitalize"><?= htmlspecialchars($item['estado']) ?></span>
                    <span style="font-size:.85rem;color:#555"><?= $item['total'] ?> <small style="color:#aaa">(<?= $pct ?>%)</small></span>
                </div>
                <div style="height:8px;background:#f0f0f0;border-radius:6px;overflow:hidden">
                    <div style="height:100%;width:<?= $pct ?>%;background:<?= $color ?>;border-radius:6px;transition:width .4s"></div>
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
            <p style="color:#aaa;font-size:.88rem">Sin actividad en este período.</p>
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

<!-- ── Botón guardar reporte ─────────────────────────────────────── -->
<div class="card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px">
    <div>
        <strong style="color:#1E3A6E">Guardar reporte de <?= $meses[$mes] ?> <?= $anio ?></strong>
        <div style="font-size:.82rem;color:#8a95a3;margin-top:3px">
            <?= $yaGuardado ? 'Este reporte ya fue guardado. Guardar de nuevo sobreescribirá los datos.' : 'Fija una instantánea del mes en el historial para consulta futura.' ?>
        </div>
    </div>
    <form method="POST" action="index.php?page=reportes&action=guardar"
          onsubmit="return confirm('¿Guardar el reporte de <?= $meses[$mes] ?> <?= $anio ?>?<?= $yaGuardado ? " Esto sobreescribirá el reporte existente." : "" ?>')">
        <?= Security::csrfField() ?>
        <input type="hidden" name="anio" value="<?= $anio ?>">
        <input type="hidden" name="mes"  value="<?= $mes ?>">
        <button type="submit" class="btn <?= $yaGuardado ? 'btn-warning' : 'btn-success' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:5px"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            <?= $yaGuardado ? 'Actualizar reporte' : 'Guardar reporte' ?>
        </button>
    </form>
</div>

<!-- ── Historial de reportes guardados ──────────────────────────── -->
<div style="margin-top:36px">
    <h2 style="margin-bottom:20px">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="#1E3A6E" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:6px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Historial de Reportes Guardados
    </h2>

    <?php if (empty($historial)): ?>
        <div class="card" style="text-align:center;color:#8a95a3;padding:40px">
            <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:12px"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p>Aún no hay reportes guardados. Guarda el reporte del mes actual para comenzar el historial.</p>
        </div>
    <?php else: ?>
        <div class="hist-grid">
        <?php foreach ($historial as $h): ?>
            <div class="hist-card">
                <div class="hist-month"><?= $meses[(int)$h['mes']] ?> <?= $h['anio'] ?></div>
                <div class="hist-meta">
                    <div>Guardado: <?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></div>
                    <?php if ($h['generado_por']): ?>
                        <div>Por: <?= htmlspecialchars($h['generado_por']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="hist-actions">
                    <a href="index.php?page=reportes&action=ver&id=<?= $h['id'] ?>"
                       class="btn btn-primary" style="padding:7px 13px;font-size:.82rem">
                       Ver reporte
                    </a>
                    <a href="index.php?page=reportes&action=exportar&id=<?= $h['id'] ?>"
                       class="btn" style="background:#6c757d;padding:7px 13px;font-size:.82rem"
                       target="_blank">
                       PDF
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
