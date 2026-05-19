<?php
require_once __DIR__ . '/../../config/security.php';
Security::setSecurityHeaders();

$errors     = $_SESSION['auth_errors'] ?? [];
$credential = $_SESSION['auth_credential'] ?? '';
$msg        = $_GET['msg'] ?? '';
unset($_SESSION['auth_errors'], $_SESSION['auth_credential']);

$csrfField = Security::csrfField();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Biblioteca Fusalmo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ── Variables (paleta del proyecto) ── */
        :root {
            --color-primary:   #1E3A6E;
            --color-nav:       #1A4FA0;
            --color-success:   #27AE60;
            --color-bg:        #F5F7FA;
            --color-alert:     #F4A726;
            --color-error:     #E74C3C;
            --color-white:     #ffffff;
            --color-text:      #2c3e50;
            --color-muted:     #6b7c93;
            --color-border:    #d1dce8;
            --font-title:      'Poppins', sans-serif;
            --font-body:       'Open Sans', sans-serif;
            --radius:          10px;
            --shadow:          0 8px 32px rgba(30,58,110,0.13);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background: var(--color-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Fondo decorativo */
        body::before {
            content: '';
            position: fixed;
            top: -120px; left: -120px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(26,79,160,0.18) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -100px; right: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(39,174,96,0.12) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }

        /* ── Card principal ── */
        .login-card {
            background: var(--color-white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 48px 44px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Logo / Header ── */
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-circle {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-nav));
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(30,58,110,0.25);
        }

        .logo-circle span {
            font-family: var(--font-title);
            font-size: 28px;
            font-weight: 700;
            color: var(--color-white);
        }

        .login-header h1 {
            font-family: var(--font-title);
            font-size: 20px;
            font-weight: 700;
            color: var(--color-primary);
            line-height: 1.3;
        }

        .login-header p {
            font-size: 13px;
            color: var(--color-muted);
            margin-top: 4px;
        }

        /* ── Alertas ── */
        .alert {
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-error {
            background: rgba(231,76,60,0.08);
            border-left: 4px solid var(--color-error);
            color: #c0392b;
        }
        .alert-success {
            background: rgba(39,174,96,0.08);
            border-left: 4px solid var(--color-success);
            color: #1e8449;
        }
        .alert ul { padding-left: 16px; margin: 0; }
        .alert li { margin-top: 4px; }

        /* ── Formulario ── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 7px;
            font-family: var(--font-title);
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-muted);
            font-size: 14px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--radius);
            font-family: var(--font-body);
            font-size: 14px;
            color: var(--color-text);
            background: #fafbfc;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--color-nav);
            box-shadow: 0 0 0 3px rgba(26,79,160,0.12);
            background: var(--color-white);
        }

        .form-control::placeholder { color: #b0bec5; }

        /* Toggle contraseña */
        .toggle-pass {
            position: absolute;
            right: 2px;
            top: 2px;
            bottom: 2px;
            width: 38px;
            background: transparent;
            border: none;
            border-radius: 0 calc(var(--radius) - 2px) calc(var(--radius) - 2px) 0;
            cursor: pointer;
            color: var(--color-muted);
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: var(--color-nav); }
        .toggle-pass:focus { outline: none; box-shadow: none; }

        /* ── Recordarme ── */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13.5px;
        }

        .remember-row label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--color-muted);
            cursor: pointer;
        }

        .remember-row input[type="checkbox"] {
            accent-color: var(--color-nav);
            width: 15px; height: 15px;
        }

        .forgot-link {
            color: var(--color-nav);
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* ── Botón ── */
        .btn-primary {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--color-nav), var(--color-primary));
            color: var(--color-white);
            border: none;
            border-radius: var(--radius);
            font-family: var(--font-title);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(26,79,160,0.3);
        }

        .btn-primary:hover {
            opacity: 0.93;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(26,79,160,0.35);
        }
        .btn-primary:active { transform: translateY(0); }

        /* ── Footer ── */
        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 12px;
            color: var(--color-muted);
        }

        /* ── Responsivo ── */
        @media (max-width: 480px) {
            .login-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="logo-circle"><span>F</span></div>
        <h1>Biblioteca Fusalmo</h1>
        <p>Sistema de Gestión</p>
    </div>

    <?php if ($msg === 'sesion_cerrada'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>Sesión cerrada correctamente.</span>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= Security::escape($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?page=login&action=process" novalidate>
        <?= $csrfField ?>

        <div class="form-group">
            <label for="credential">Usuario o correo electrónico</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input
                    type="text"
                    id="credential"
                    name="credential"
                    class="form-control"
                    placeholder="ej. juan@fusalmo.org"
                    value="<?= Security::escape($credential) ?>"
                    autocomplete="username"
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="••••••••••"
                    autocomplete="current-password"
                    required
                    style="padding-right: 42px;"
                >
                <button type="button" class="toggle-pass" onclick="togglePassword()" title="Mostrar/ocultar contraseña">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <div class="remember-row">
            <label>
                <input type="checkbox" name="recordar">
                Recordarme
            </label>
            <a href="index.php?page=recuperar" class="forgot-link">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-sign-in-alt" style="margin-right:8px;"></i>
            Iniciar sesión
        </button>
    </form>

    <div class="login-footer">
        &copy; <?= date('Y') ?> Fundación Fusalmo — DSS 404 G03T
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Validación client-side básica
document.querySelector('form').addEventListener('submit', function(e) {
    const cred = document.getElementById('credential').value.trim();
    const pass = document.getElementById('password').value;
    if (!cred || !pass) {
        e.preventDefault();
        alert('Por favor completa todos los campos.');
    }
});
</script>
</body>
</html>
