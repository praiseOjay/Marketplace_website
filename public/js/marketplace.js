/**
 * Marketplace — Interactive UI Logic
 * Feature: Password Visibility Toggle (Show / Hide password)
 */

document.addEventListener('DOMContentLoaded', () => {
    // Universal Password Visibility Toggle
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

        // Maintain focus and cursor position at end of input
        const val = passwordInput.value;
        passwordInput.focus();
        passwordInput.setSelectionRange(val.length, val.length);
    });
});
