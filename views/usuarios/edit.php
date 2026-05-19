<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Editar Usuario
                    </h4>
                </div>
                <div class="card-body">

                    <!-- Mensajes de error -->
                    <?php if (!empty($_SESSION['edit_errors'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-circle"></i>
                            <ul class="mb-0 ms-3">
                                <?php foreach ($_SESSION['edit_errors'] as $error): ?>
                                    <li><?= Security::escape($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['edit_errors']); ?>
                    <?php endif; ?>

                    <!-- Formulario -->
                    <form method="POST" action="index.php?page=usuarios&action=process-edit">

                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="bi bi-person"></i> Nombre Completo
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   class="form-control" 
                                   value="<?= Security::escape($_SESSION['edit_data']['nombre'] ?? $usuario['nombre']) ?>"
                                   required 
                                   minlength="3">
                            <small class="text-muted">Mínimo 3 caracteres</small>
                        </div>
                        <?php unset($_SESSION['edit_data']); ?>

                        <!-- Correo -->
                        <div class="mb-3">
                            <label for="correo" class="form-label">
                                <i class="bi bi-envelope"></i> Correo Electrónico
                            </label>
                            <input type="email" 
                                   id="correo" 
                                   name="correo" 
                                   class="form-control" 
                                   value="<?= Security::escape($usuario['correo']) ?>"
                                   required>
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label for="telefono" class="form-label">
                                <i class="bi bi-telephone"></i> Teléfono
                            </label>
                            <input type="tel" 
                                   id="telefono" 
                                   name="telefono" 
                                   class="form-control" 
                                   value="<?= Security::escape($usuario['telefono'] ?: '') ?>">
                        </div>

                        <!-- Rol -->
                        <div class="mb-3">
                            <label for="rol" class="form-label">
                                <i class="bi bi-shield"></i> Rol
                            </label>
                            <select id="rol" name="rol" class="form-select" required>
                                <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>
                                    Usuario
                                </option>
                                <option value="bibliotecario" <?= $usuario['rol'] === 'bibliotecario' ? 'selected' : '' ?>>
                                    Bibliotecario
                                </option>
                                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>
                                    Administrador
                                </option>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?page=usuarios" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
