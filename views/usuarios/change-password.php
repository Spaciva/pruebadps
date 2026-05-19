<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-key"></i> Cambiar Contraseña
                    </h4>
                </div>
                <div class="card-body">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Usuario:</strong> <?= Security::escape($usuario['nombre']) ?>
                        (<?= Security::escape($usuario['correo']) ?>)
                    </div>

                    <!-- Mensajes de error -->
                    <?php if (!empty($_SESSION['pass_errors'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-circle"></i>
                            <ul class="mb-0 ms-3">
                                <?php foreach ($_SESSION['pass_errors'] as $error): ?>
                                    <li><?= Security::escape($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['pass_errors']); ?>
                    <?php endif; ?>

                    <!-- Formulario -->
                    <form method="POST" action="index.php?page=usuarios&action=process-change-password">

                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                        <!-- Nueva contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Nueva Contraseña
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   required 
                                   minlength="8"
                                   placeholder="Mínimo 8 caracteres">
                            <div class="progress mt-2" style="height:6px;" id="strengthBar" hidden>
                                <div class="progress-bar" id="strengthFill" role="progressbar" style="width:0%"></div>
                            </div>
                            <small id="strengthLabel" class="text-muted"></small>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle"></i>
                                Recomendaciones:
                                <ul class="mb-0">
                                    <li>Mínimo 8 caracteres</li>
                                    <li>Incluir mayúsculas y minúsculas</li>
                                    <li>Incluir números</li>
                                    <li>Incluir caracteres especiales (!@#$%)</li>
                                </ul>
                            </small>
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="mb-3">
                            <label for="confirm" class="form-label">
                                <i class="bi bi-lock-fill"></i> Confirmar Contraseña
                            </label>
                            <input type="password" 
                                   id="confirm" 
                                   name="confirm" 
                                   class="form-control" 
                                   required 
                                   minlength="8"
                                   placeholder="Repite la contraseña">
                            <small id="matchLabel" class="text-muted"></small>
                        </div>

                        <!-- Botones -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?page=usuarios" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cambiar Contraseña
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const passInput  = document.getElementById('password');
    const confirmInput = document.getElementById('confirm');
    const strengthBar  = document.getElementById('strengthBar');
    const strengthFill = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    const matchLabel   = document.getElementById('matchLabel');

    const levels = [
        { pct: '0%',   color: 'bg-secondary', text: '' },
        { pct: '25%',  color: 'bg-danger',    text: 'Débil' },
        { pct: '50%',  color: 'bg-warning',   text: 'Regular' },
        { pct: '75%',  color: 'bg-primary',   text: 'Buena' },
        { pct: '100%', color: 'bg-success',   text: 'Muy segura' },
    ];

    passInput.addEventListener('input', () => {
        const v = passInput.value;
        let score = 0;
        if (v.length >= 8)           score++;
        if (/[A-Z]/.test(v))         score++;
        if (/[0-9]/.test(v))         score++;
        if (/[^A-Za-z0-9]/.test(v))  score++;

        strengthBar.hidden = v.length === 0;
        strengthFill.className = 'progress-bar ' + levels[score].color;
        strengthFill.style.width = levels[score].pct;
        strengthLabel.textContent = levels[score].text;

        updateMatch();
    });

    confirmInput.addEventListener('input', updateMatch);

    function updateMatch() {
        const p = passInput.value;
        const c = confirmInput.value;
        if (c.length === 0) { matchLabel.textContent = ''; return; }
        if (p === c) {
            matchLabel.className = 'text-success';
            matchLabel.textContent = '✔ Las contraseñas coinciden.';
        } else {
            matchLabel.className = 'text-danger';
            matchLabel.textContent = '✖ Las contraseñas no coinciden.';
        }
    }

    document.querySelector('form').addEventListener('submit', function (e) {
        const p = passInput.value;
        const c = confirmInput.value;
        if (p !== c) {
            e.preventDefault();
            matchLabel.className = 'text-danger';
            matchLabel.textContent = '✖ Las contraseñas no coinciden.';
            confirmInput.focus();
        }
    });
})();
</script>

</body>
</html>
