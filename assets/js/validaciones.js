/**
 * validaciones.js — Biblioteca Fusalmo
 * Utilidades de validación reutilizables para las vistas MVC.
 */

/**
 * Valida formato ISBN-10 o ISBN-13 (ignora guiones y espacios).
 * @param {string} isbn
 * @returns {boolean}
 */
function isValidISBN(isbn) {
    const clean = isbn.replace(/[\s\-]/g, '');
    return /^[0-9]{9}[0-9Xx]$/.test(clean) || /^97[89][0-9]{10}$/.test(clean);
}

/**
 * Evalúa la fortaleza de una contraseña.
 * @param {string} password
 * @returns {{ score: number, label: string, color: string }}
 */
function evaluatePasswordStrength(password) {
    let score = 0;
    if (password.length >= 8)            score++;
    if (/[A-Z]/.test(password))          score++;
    if (/[0-9]/.test(password))          score++;
    if (/[^A-Za-z0-9]/.test(password))   score++;

    const levels = [
        { label: '',           color: '#e0e0e0' },
        { label: 'Débil',      color: '#E74C3C' },
        { label: 'Regular',    color: '#F4A726' },
        { label: 'Buena',      color: '#1A4FA0' },
        { label: 'Muy segura', color: '#27AE60' },
    ];
    return { score, ...levels[score] };
}

/**
 * Adjunta un medidor de fortaleza de contraseña a un campo de tipo password.
 * @param {HTMLInputElement} input  Campo de la contraseña.
 * @param {HTMLElement}      bar    Elemento de barra (se aplica width + background).
 * @param {HTMLElement}      label  Elemento donde se muestra el texto.
 */
function attachPasswordStrength(input, bar, label) {
    input.addEventListener('input', () => {
        const { score, label: text, color } = evaluatePasswordStrength(input.value);
        const pct = ['0%', '25%', '50%', '75%', '100%'][score];
        bar.style.width      = pct;
        bar.style.background = color;
        if (label) { label.textContent = text; label.style.color = color; }
    });
}

/**
 * Adjunta validación de coincidencia de contraseñas a un campo de confirmación.
 * @param {HTMLInputElement} passInput    Campo contraseña original.
 * @param {HTMLInputElement} confirmInput Campo de confirmación.
 * @param {HTMLElement}      feedbackEl   Elemento donde se muestra el mensaje.
 */
function attachPasswordMatch(passInput, confirmInput, feedbackEl) {
    function check() {
        if (!confirmInput.value) { feedbackEl.textContent = ''; return; }
        if (passInput.value === confirmInput.value) {
            feedbackEl.className = 'text-success';
            feedbackEl.textContent = '✔ Las contraseñas coinciden.';
        } else {
            feedbackEl.className = 'text-danger';
            feedbackEl.textContent = '✖ Las contraseñas no coinciden.';
        }
    }
    passInput.addEventListener('input', check);
    confirmInput.addEventListener('input', check);
}
