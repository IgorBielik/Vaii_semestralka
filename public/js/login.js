// Login form validation and alert handling
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="login"]');
    if (!form) return;

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Validácia email formátu
    function validateEmailFormat(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Zobrazenie chyby
    function showError(input, message) {
        // Odstráň starú chybu ak existuje
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Pridaj novú chybu
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-danger small mt-1';
        errorDiv.textContent = message;
        input.classList.add('is-invalid');
        input.parentElement.appendChild(errorDiv);
    }

    // Odstránenie chyby
    function clearError(input) {
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        input.classList.remove('is-invalid');
    }

    // Email validácia na blur
    if (emailInput) {
        emailInput.addEventListener('blur', function () {
            const email = this.value.trim();
            clearError(this);

            if (email !== '' && !validateEmailFormat(email)) {
                showError(this, 'Email má nevalidný formát (napr. user@example.com).');
            }
        });

        // Čisti chybu pri písaní
        emailInput.addEventListener('input', function () {
            clearError(this);
        });
    }

    // Form submit validácia
    form.addEventListener('submit', function (e) {
        const email = emailInput.value.trim();
        const password = passwordInput.value;

        clearError(emailInput);
        clearError(passwordInput);

        let isValid = true;

        if (email === '') {
            showError(emailInput, 'Email je povinný.');
            isValid = false;
        } else if (!validateEmailFormat(email)) {
            showError(emailInput, 'Email má nevalidný formát (napr. user@example.com).');
            isValid = false;
        }

        if (password === '') {
            showError(passwordInput, 'Heslo je povinné.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Alert auto-dismiss
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const closeBtn = alert.querySelector('[data-bs-dismiss="alert"]');

        // Funkcia na odstránenie alertu
        function removeAlert() {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                removeAlert();
            });
        }

        // Auto-dismiss po 5 sekundách
        setTimeout(function () {
            removeAlert();
        }, 5000);
    });
});
