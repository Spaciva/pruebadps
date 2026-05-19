<?php
require_once __DIR__ . '/../../config/security.php';
Security::setSecurityHeaders();

$errors  = $_SESSION['reg_errors'] ?? [];
$old     = $_SESSION['reg_data'] ?? [];
unset($_SESSION['reg_errors'], $_SESSION['reg_data']);

$csrfField = Security::csrfField();
$userName  = Security::escape(Session::get('user_name') ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario — Biblioteca Fusalmo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BibliotecaMVC/assets/css/estilos.css">
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="mb-0">Registrar Usuario</h4>
                    <p class="mb-0 text-white-50">Crea una nueva cuenta en el sistema</p>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= Security::escape($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=register&action=process" novalidate id="regForm">
                        <?= $csrfField ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="nombre">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="ej. Ana García"
                                       value="<?= Security::escape($old['nombre'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" class="form-control" placeholder="ej. +503 7000-0000"
                                       value="<?= Security::escape($old['telefono'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="correo">Correo electrónico</label>
                            <input type="email" id="correo" name="correo" class="form-control" placeholder="ej. ana@fusalmo.org"
                                   value="<?= Security::escape($old['email'] ?? '') ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="contrasena">Contraseña</label>
                                <input type="password" id="contrasena" name="contrasena" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                                <div class="strength-label" id="strengthLabel">Ingresa una contraseña</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="confirmar">Confirmar contraseña</label>
                                <input type="password" id="confirmar" name="confirmar" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="rol">Rol del usuario</label>
                            <select id="rol" name="<?= Session::get('user_role') === 'bibliotecario' ? '' : 'rol' ?>" class="form-select" required <?= (Session::get('user_role') === 'bibliotecario') ? 'disabled' : '' ?>>
                                <option value="">— Seleccionar rol —</option>
                                <?php if(Session::get('user_role') !== 'bibliotecario'): ?>
                                <option value="admin" <?= ($old['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="bibliotecario" <?= ($old['rol'] ?? '') === 'bibliotecario' ? 'selected' : '' ?>>Bibliotecario</option>
                                <?php endif; ?>
                                <option value="usuario" <?= (Session::get('user_role') === 'bibliotecario' || ($old['rol'] ?? '') === 'usuario') ? 'selected' : '' ?>>Usuario</option>
                            </select>
                            <?php if(Session::get('user_role') === 'bibliotecario'): ?>
                            <input type="hidden" name="rol" value="usuario">
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php?page=usuarios" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const passInput = document.getElementById('contrasena');
const fill      = document.getElementById('strengthFill');
const label     = document.getElementById('strengthLabel');

passInput.addEventListener('input', () => {
    const v = passInput.value;
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const levels = [
        { pct: '0%', color: '#e0e0e0', text: 'Ingresa una contraseña' },
        { pct: '25%', color: '#E74C3C', text: 'Débil' },
        { pct: '50%', color: '#F4A726', text: 'Regular' },
        { pct: '75%', color: '#1A4FA0', text: 'Buena' },
        { pct: '100%', color: '#27AE60', text: 'Muy segura' },
    ];

    fill.style.width      = levels[score].pct;
    fill.style.background = levels[score].color;
    label.textContent     = levels[score].text;
    label.style.color     = levels[score].color;
});

document.getElementById('regForm').addEventListener('submit', function(e) {
    const pass    = document.getElementById('contrasena').value;
    const confirm = document.getElementById('confirmar').value;
    if (pass !== confirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden.');
    }
});
</script>
</body>
</html>
