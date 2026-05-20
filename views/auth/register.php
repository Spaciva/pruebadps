<?php
require_once __DIR__ . '/../../config/security.php';

$errors = $_SESSION['reg_errors'] ?? [];
$old    = $_SESSION['reg_data'] ?? [];

unset($_SESSION['reg_errors']);
unset($_SESSION['reg_data']);

$csrfField = Security::csrfField();

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">

    <h1>👤 Registrar Usuario</h1>

    <p style="color:#6b7c93;margin-bottom:25px;">
        Crea una nueva cuenta en el sistema
    </p>

    <?php if(!empty($errors)): ?>

        <div class="alert alert-danger">

            <ul style="margin:0; padding-left:20px;">

                <?php foreach($errors as $e): ?>

                    <li>

                        <?= Security::escape($e) ?>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>


    <form
    method="POST"
    action="index.php?page=register&action=process"
    id="regForm">

        <?= $csrfField ?>

        <div class="form-row">

            <div>

                <label for="nombre">

                    Nombre completo

                </label>

                <input
                type="text"
                id="nombre"
                name="nombre"
                class="form-control"
                placeholder="Ej: Ana García"
                value="<?= Security::escape($old['nombre'] ?? '') ?>"
                required>

            </div>


            <div>

                <label for="telefono">

                    Teléfono

                </label>

                <input
                type="text"
                id="telefono"
                name="telefono"
                class="form-control"
                placeholder="Ej: +503 7000-0000"
                value="<?= Security::escape($old['telefono'] ?? '') ?>">

            </div>

        </div>


        <div style="margin-bottom:20px;">

            <label for="correo">

                Correo electrónico

            </label>

            <input
            type="email"
            id="correo"
            name="correo"
            class="form-control"
            placeholder="Ej: usuario@fusalmo.org"
            value="<?= Security::escape($old['correo'] ?? '') ?>"
            required>

        </div>


        <div class="form-row">

            <div>

                <label for="contrasena">

                    Contraseña

                </label>

                <input
                type="password"
                id="contrasena"
                name="contrasena"
                class="form-control"
                placeholder="••••••••"
                required>

                <div class="strength-bar">

                    <div
                    class="strength-fill"
                    id="strengthFill">

                    </div>

                </div>

                <div
                class="strength-label"
                id="strengthLabel">

                    Ingresa una contraseña

                </div>

            </div>


            <div>

                <label for="confirmar">

                    Confirmar contraseña

                </label>

                <input
                type="password"
                id="confirmar"
                name="confirmar"
                class="form-control"
                placeholder="••••••••"
                required>

            </div>

        </div>


        <div style="margin-top:20px;">

            <label for="rol">

                Rol del usuario

            </label>

            <select
            id="rol"
            name="rol"
            class="form-control"
            required>

                <option value="">
                    — Seleccionar rol —
                </option>

                <option value="admin"
                <?= ($old['rol'] ?? '')==='admin' ? 'selected':'' ?>>

                    Administrador

                </option>

                <option value="bibliotecario"
                <?= ($old['rol'] ?? '')==='bibliotecario' ? 'selected':'' ?>>

                    Bibliotecario

                </option>

                <option value="usuario"
                <?= ($old['rol'] ?? '')==='usuario' ? 'selected':'' ?>>

                    Usuario

                </option>

            </select>


            <div class="role-info">

                <span class="role-badge role-admin">

                    👑 Admin

                </span>

                <span class="role-badge role-bib">

                    📚 Bibliotecario

                </span>

                <span class="role-badge role-user">

                    👤 Usuario

                </span>

            </div>

        </div>


        <div
        style="
        margin-top:25px;
        display:flex;
        gap:10px;">

            <button
            type="submit"
            class="btn btn-primary">

                Guardar Usuario

            </button>

            <a
            href="index.php?page=usuarios"
            class="btn btn-secondary">

                Cancelar

            </a>

        </div>

    </form>

</div>


<script>

// Fortalecer contraseña

const passInput = document.getElementById('contrasena');
const fill = document.getElementById('strengthFill');
const label = document.getElementById('strengthLabel');

passInput.addEventListener('input',()=>{

    const v=passInput.value;

    let score=0;

    if(v.length>=8) score++;
    if(/[A-Z]/.test(v)) score++;
    if(/[0-9]/.test(v)) score++;
    if(/[^A-Za-z0-9]/.test(v)) score++;

    const levels=[

        {
            width:'0%',
            color:'#ddd',
            text:'Ingresa una contraseña'
        },

        {
            width:'25%',
            color:'#dc3545',
            text:'Débil'
        },

        {
            width:'50%',
            color:'#ffc107',
            text:'Regular'
        },

        {
            width:'75%',
            color:'#0d6efd',
            text:'Buena'
        },

        {
            width:'100%',
            color:'#28a745',
            text:'Muy segura'
        }

    ];

    fill.style.width=levels[score].width;
    fill.style.background=levels[score].color;

    label.textContent=levels[score].text;
    label.style.color=levels[score].color;

});


// Verificar confirmación

document.getElementById(
'regForm'
).addEventListener(
'submit',
function(e){

const pass=
document.getElementById(
'contrasena'
).value;

const confirm=
document.getElementById(
'confirmar'
).value;

if(pass!==confirm){

e.preventDefault();

alert(
'Las contraseñas no coinciden'
);

}

});

</script>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>