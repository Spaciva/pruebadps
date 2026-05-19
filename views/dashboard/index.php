<?php require_once __DIR__.'/../layouts/header.php'; ?>

<style>
.dashboard-hero {
    text-align: center;
    margin-bottom: 35px;
}
.dashboard-hero img {
    max-width: 380px;
    width: 100%;
    height: auto;
}
.dashboard-welcome {
    margin-top: 12px;
    font-size: 1rem;
    color: #6c757d;
    letter-spacing: 0.03em;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 22px;
}

.dash-card {
    background: white;
    border-radius: 18px;
    padding: 32px 24px 28px;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    box-shadow: 0 4px 18px rgba(0,0,0,.07);
    border-top: 4px solid transparent;
    transition: transform .18s ease, box-shadow .18s ease;
    cursor: pointer;
}
.dash-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 28px rgba(0,0,0,.13);
    text-decoration: none;
    color: inherit;
}

.dash-card-icon {
    width: 62px;
    height: 62px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.dash-card-icon svg {
    width: 30px;
    height: 30px;
}

.dash-card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1E3A6E;
    text-align: center;
}
.dash-card-desc {
    font-size: 0.82rem;
    color: #8a95a3;
    text-align: center;
    line-height: 1.5;
}

/* Colores por módulo */
.dash-card--libros      { border-top-color: #1A4FA0; }
.dash-card--libros      .dash-card-icon { background: rgba(26,79,160,.12); color:#1A4FA0; }
.dash-card--catalogo    { border-top-color: #27ae60; }
.dash-card--catalogo    .dash-card-icon { background: rgba(39,174,96,.12); color:#27ae60; }
.dash-card--categorias  { border-top-color: #17a2b8; }
.dash-card--categorias  .dash-card-icon { background: rgba(23,162,184,.12); color:#17a2b8; }
.dash-card--prestamos   { border-top-color: #e67e22; }
.dash-card--prestamos   .dash-card-icon { background: rgba(230,126,34,.12); color:#e67e22; }
.dash-card--misprestamos{ border-top-color: #e67e22; }
.dash-card--misprestamos .dash-card-icon { background: rgba(230,126,34,.12); color:#e67e22; }
.dash-card--usuarios    { border-top-color: #8e44ad; }
.dash-card--usuarios    .dash-card-icon { background: rgba(142,68,173,.12); color:#8e44ad; }
.dash-card--reportes    { border-top-color: #c0392b; }
.dash-card--reportes    .dash-card-icon { background: rgba(192,57,43,.12); color:#c0392b; }
</style>

<div class="card">

    <!-- HERO / LOGO -->
    <div class="dashboard-hero">
        <img src="/BibliotecaMVC/assets/images/banner-fusalmo.png" alt="Logo Fusalmo">
        <p class="dashboard-welcome">Sistema de Gestión de Biblioteca</p>
    </div>

    <!-- CARDS POR ROL -->
    <?php $userRole = Session::get('user_role') ?? ''; ?>
    <div class="dashboard-grid">

        <?php if ($userRole === 'admin' || $userRole === 'bibliotecario'): ?>
        <a href="index.php?page=libros" class="dash-card dash-card--libros">
            <div class="dash-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <span class="dash-card-title">Libros</span>
            <span class="dash-card-desc">Gestiona el catálogo completo de libros del sistema</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'usuario'): ?>
        <a href="index.php?page=catalogo-libros" class="dash-card dash-card--catalogo">
            <div class="dash-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <span class="dash-card-title">Catálogo de Libros</span>
            <span class="dash-card-desc">Explora los libros disponibles y realiza préstamos</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'usuario' || $userRole === 'bibliotecario'): ?>
        <a href="index.php?page=mis-prestamos" class="dash-card dash-card--misprestamos">
            <div class="dash-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
            </div>
            <span class="dash-card-title">Mis Préstamos</span>
            <span class="dash-card-desc">Consulta y gestiona tus préstamos activos</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'admin'): ?>
        <a href="index.php?page=categorias" class="dash-card dash-card--categorias">
            <div class="dash-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <span class="dash-card-title">Categorías</span>
            <span class="dash-card-desc">Administra las categorías de clasificación de libros</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'admin' || $userRole === 'bibliotecario'): ?>
        <a href="index.php?page=prestamos" class="dash-card dash-card--prestamos">
            <div class="dash-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="dash-card-title">Préstamos</span>
            <span class="dash-card-desc">Registra, consulta y gestiona todos los préstamos</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'admin'): ?>
        <a href="index.php?page=reportes" class="dash-card dash-card--reportes">
            <div class="dash-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <span class="dash-card-title">Reportes</span>
            <span class="dash-card-desc">Consulta y exporta reportes del sistema</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'admin' || $userRole === 'bibliotecario'): ?>
        <a href="index.php?page=usuarios" class="dash-card dash-card--usuarios">
            <div class="dash-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <span class="dash-card-title">Usuarios</span>
            <span class="dash-card-desc">Administra los usuarios y sus permisos de acceso</span>
        </a>
        <?php endif; ?>

    </div>

</div>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>
