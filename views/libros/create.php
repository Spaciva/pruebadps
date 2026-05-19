<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<?php
    $old = $_SESSION['libro_data'] ?? [];
    $titulo = $old['titulo'] ?? '';
    $autorInput = $old['autorInput'] ?? '';
    $categoriaId = $old['categoriaId'] ?? '';
    $isbn = $old['isbn'] ?? '';
    $cantidad = $old['cantidad'] ?? '';
    $descripcion = $old['descripcion'] ?? '';
    unset($_SESSION['libro_data']);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Agregar Libro</h4>
                </div>
                <div class="card-body">

                    <?php if (!empty($_SESSION['libro_errors'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ms-3">
                                <?php foreach ($_SESSION['libro_errors'] as $error): ?>
                                    <li><?= Security::escape($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['libro_errors']); ?>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=libros&action=process-create">
                        <?= Security::csrfField() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="titulo" class="form-label"><i class="bi bi-book"></i> Título</label>
                                <input type="text" id="titulo" name="titulo" class="form-control" required minlength="3" value="<?= Security::escape($titulo) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="isbn" class="form-label"><i class="bi bi-barcode"></i> ISBN</label>
                                <input type="text" id="isbn" name="isbn" class="form-control" required
                                       placeholder="ej. 978-3-16-148410-0"
                                       pattern="^[0-9]{9}[0-9Xx]$|^[0-9]{13}$|^97[89][0-9\-]{10,15}$"
                                       title="ISBN de 10 o 13 dígitos (guiones opcionales)"
                                       value="<?= Security::escape($isbn) ?>">
                                <small class="text-muted">ISBN-10 o ISBN-13 (guiones opcionales)</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="autor" class="form-label"><i class="bi bi-person-lines-fill"></i> Autor</label>
                                <input list="autores" id="autor" name="autor" class="form-control" required minlength="3" value="<?= Security::escape($autorInput) ?>" autocomplete="off">
                                <datalist id="autores">
                                    <?php foreach ($autores as $autor): ?>
                                        <option value="<?= Security::escape($autor['nombre']) ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                                <div class="form-text">Escribe el nombre del autor. Si no existe, se agregará automáticamente.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categoria_id" class="form-label"><i class="bi bi-tag"></i> Categoría</label>
                                <select id="categoria_id" name="categoria_id" class="form-select" required>
                                    <option value="">-- Seleccionar categoría --</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>" <?= $categoriaId == $categoria['id'] ? 'selected' : '' ?>><?= Security::escape($categoria['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cantidad" class="form-label"><i class="bi bi-stack"></i> Cantidad</label>
                            <input type="number" id="cantidad" name="cantidad" class="form-control" required min="1" value="<?= Security::escape($cantidad) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label"><i class="bi bi-file-text"></i> Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="3"><?= Security::escape($descripcion) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?page=libros" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Libro
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
document.querySelector('form').addEventListener('submit', function (e) {
    const isbn = document.getElementById('isbn').value.replace(/[\s\-]/g, '');
    const valid = /^[0-9]{9}[0-9Xx]$/.test(isbn) || /^97[89][0-9]{10}$/.test(isbn);
    if (!valid) {
        e.preventDefault();
        document.getElementById('isbn').classList.add('is-invalid');
        let fb = document.getElementById('isbn-feedback');
        if (!fb) {
            fb = document.createElement('div');
            fb.id = 'isbn-feedback';
            fb.className = 'invalid-feedback';
            fb.textContent = 'ISBN inválido. Debe tener 10 o 13 dígitos (guiones opcionales).';
            document.getElementById('isbn').after(fb);
        }
    } else {
        document.getElementById('isbn').classList.remove('is-invalid');
    }
});
document.getElementById('isbn').addEventListener('input', function () {
    this.classList.remove('is-invalid');
});
</script>

</body>
</html>
