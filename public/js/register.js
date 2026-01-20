// Frontend validation + AJAX email check for registration form
// Requirements:
// - check that password confirmation matches
// - check that password has at least 8 characters
// - check via AJAX whether email already exists in DB

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="register"]');
    if (!form) return;

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirm');

    if (!emailInput || !passwordInput || !passwordConfirmInput) return;

    // Create / find a place for inline error messages
    function ensureErrorSpan(input) {
        let span = input.parentElement.querySelector('.text-danger.register-error');
        if (!span) {
            span = document.createElement('div');
            span.className = 'text-danger small mt-1 register-error';
            input.parentElement.appendChild(span);
        }
        return span;
    }

    function clearError(input) {
        const span = input.parentElement.querySelector('.register-error');
        if (span) {
            span.textContent = '';
        }
        input.classList.remove('is-invalid');
    }

    function setError(input, message) {
        const span = ensureErrorSpan(input);
        span.textContent = message;
        input.classList.add('is-invalid');
    }

    // --- Password checks ---
    function validatePasswordLength() {
        clearError(passwordInput);
        const value = passwordInput.value || '';
        if (value.length > 0 && value.length < 8) {
            setError(passwordInput, 'Heslo musí mať aspoň 8 znakov.');
            return false;
        }
        return true;
    }

    function validatePasswordMatch() {
        clearError(passwordConfirmInput);
        const p1 = passwordInput.value || '';
        const p2 = passwordConfirmInput.value || '';
        if (p2.length > 0 && p1 !== p2) {
            setError(passwordConfirmInput, 'Heslá sa nezhodujú.');
            return false;
        }
        return true;
    }

    passwordInput.addEventListener('input', function () {
        validatePasswordLength();
        // When password changes, re-check confirmation
        if (passwordConfirmInput.value.length > 0) {
            validatePasswordMatch();
        }
    });

    passwordConfirmInput.addEventListener('input', function () {
        validatePasswordMatch();
    });

    // --- AJAX email check ---
    let emailCheckTimeout = null;
    let lastCheckedEmail = '';
    let lastEmailExists = false;

    function checkEmailAjax() {
        const email = emailInput.value.trim();
        clearError(emailInput);

        if (email === '') {
            return;
        }

        // If user types same email as last checked and result was that it exists, keep the info
        if (email === lastCheckedEmail && lastEmailExists) {
            setError(emailInput, 'Používateľ s týmto emailom už existuje.');
            return;
        }

        // Build URL for AJAX check – we add a=checkEmail on top of auth controller
        const url = '?c=auth&a=checkEmail&email=' + encodeURIComponent(email);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then(resp => resp.ok ? resp.json() : Promise.reject(resp.status))
            .then(data => {
                if (!data) return;
                const exists = !!data.exists;
                lastCheckedEmail = email;
                lastEmailExists = exists;

                if (exists) {
                    setError(emailInput, 'Používateľ s týmto emailom už existuje.');
                } else {
                    clearError(emailInput);
                }
            })
            .catch(() => {
                // On error, do not block user, just do nothing.
            });
    }

    emailInput.addEventListener('input', function () {
        clearError(emailInput);
        if (emailCheckTimeout) {
            clearTimeout(emailCheckTimeout);
        }
        emailCheckTimeout = setTimeout(checkEmailAjax, 500);
    });

    emailInput.addEventListener('blur', function () {
        // On blur, check immediately
        if (emailCheckTimeout) {
            clearTimeout(emailCheckTimeout);
            emailCheckTimeout = null;
        }
        checkEmailAjax();
    });

    // --- Final form submit validation ---
    form.addEventListener('submit', function (e) {
        let ok = true;

        if (!validatePasswordLength()) {
            ok = false;
        }
        if (!validatePasswordMatch()) {
            ok = false;
        }

        if (lastEmailExists) {
            setError(emailInput, 'Používateľ s týmto emailom už existuje.');
            ok = false;
        }

        if (!ok) {
            e.preventDefault();
        }
    });
});
