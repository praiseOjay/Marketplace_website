/**
 * Marketplace — Global Interactive Logic
 * Features:
 * 1. Password Visibility Toggle (Show / Hide password)
 * 2. Google reCAPTCHA v3 Automated Form Protection
 */

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 1. Universal Password Visibility Toggle
    // ==========================================
    document.addEventListener('click', (event) => {
        const toggleBtn = event.target.closest('.mp-password-toggle, [data-password-toggle]');
        if (!toggleBtn) return;

        event.preventDefault();
        event.stopPropagation();

        const wrapper = toggleBtn.closest('.mp-password-wrapper') || toggleBtn.parentElement;
        if (!wrapper) return;

        const passwordInput = wrapper.querySelector('input');
        if (!passwordInput) return;

        const icon = toggleBtn.querySelector('i');
        const isPassword = passwordInput.getAttribute('type') === 'password';

        if (isPassword) {
            passwordInput.setAttribute('type', 'text');
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
            toggleBtn.setAttribute('aria-label', 'Hide password');
            toggleBtn.setAttribute('title', 'Hide password');
        } else {
            passwordInput.setAttribute('type', 'password');
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
            toggleBtn.setAttribute('aria-label', 'Show password');
            toggleBtn.setAttribute('title', 'Show password');
        }

        // Maintain cursor position at end of input
        const val = passwordInput.value;
        passwordInput.focus();
        passwordInput.setSelectionRange(val.length, val.length);
    });

    // ==========================================
    // 2. Google reCAPTCHA v3 Automatic Binding
    // ==========================================
    const siteKey = window.RECAPTCHA_SITE_KEY;
    if (siteKey && siteKey.trim() !== '') {
        const forms = document.querySelectorAll('form[data-recaptcha-action], form.recaptcha-form');

        forms.forEach((form) => {
            let isSubmitting = false;

            form.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    return;
                }

                // If grecaptcha is loaded
                if (typeof grecaptcha !== 'undefined' && grecaptcha.execute) {
                    e.preventDefault();
                    isSubmitting = true;

                    const actionName = form.getAttribute('data-recaptcha-action') || 'submit';

                    grecaptcha.ready(function () {
                        grecaptcha.execute(siteKey, { action: actionName }).then(function (token) {
                            let tokenInput = form.querySelector('input[name="recaptcha_token"]');
                            if (!tokenInput) {
                                tokenInput = document.createElement('input');
                                tokenInput.type = 'hidden';
                                tokenInput.name = 'recaptcha_token';
                                form.appendChild(tokenInput);
                            }
                            tokenInput.value = token;

                            // Also populate g-recaptcha-response for standard compatibility
                            let gInput = form.querySelector('input[name="g-recaptcha-response"]');
                            if (!gInput) {
                                gInput = document.createElement('input');
                                gInput.type = 'hidden';
                                gInput.name = 'g-recaptcha-response';
                                form.appendChild(gInput);
                            }
                            gInput.value = token;

                            form.submit();
                        }).catch(function (error) {
                            console.warn('reCAPTCHA execution error, submitting form without token:', error);
                            form.submit();
                        });
                    });
                }
            });
        });
    }
});
