// සිංහල සටහන: navigation menu, header shadow සහ UI interactions සඳහා JavaScript.
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('[data-header]');
    const toggle = document.querySelector('[data-nav-toggle]');
    const menu = document.querySelector('[data-nav-menu]');

    const handleScroll = () => {
        if (header) header.classList.toggle('is-scrolled', window.scrollY > 6);
    };

    handleScroll();
    window.addEventListener('scroll', handleScroll);

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('is-open');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const message = button.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});

// සිංහල සටහන: forms වල name/email/phone/card fields සඳහා inline validation errors පෙන්වයි.
document.addEventListener('DOMContentLoaded', () => {
    const nameRegex = /^[\p{L}\s.'-]+$/u;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function getErrorNode(input) {
        let node = input.parentElement ? input.parentElement.querySelector('.form-error') : null;
        if (!node && input.parentElement) {
            node = document.createElement('small');
            node.className = 'form-error';
            node.setAttribute('role', 'alert');
            input.insertAdjacentElement('afterend', node);
        }
        return node;
    }

    function setError(input, message) {
        const node = getErrorNode(input);
        input.classList.toggle('is-invalid', Boolean(message));
        input.setCustomValidity(message || '');
        if (node) node.textContent = message || '';
    }

    function validateInput(input) {
        let value = input.value.trim();
        let message = '';

        if (input.hasAttribute('required') && value === '') {
            message = 'This field is required.';
        } else if (input.matches('[data-validate-name]')) {
            const cleaned = input.value.replace(/[^\p{L}\s.'-]/gu, '');
            if (cleaned !== input.value) {
                input.value = cleaned;
                value = input.value.trim();
                message = input.dataset.errorMessage || 'Only letters and spaces are allowed.';
            } else if (value !== '' && !nameRegex.test(value)) {
                message = input.dataset.errorMessage || 'Only letters and spaces are allowed.';
            }
        } else if (input.matches('[data-validate-phone]')) {
            const cleaned = input.value.replace(/\D+/g, '').slice(0, 15);
            if (cleaned !== input.value) input.value = cleaned;
            value = input.value.trim();
            if (input.hasAttribute('required') && value === '') {
                message = 'Phone number is required.';
            } else if (value !== '' && !/^[0-9]{7,15}$/.test(value)) {
                message = 'Phone number must contain 7 to 15 digits only.';
            }
        } else if (input.matches('[data-validate-email]')) {
            if (value !== '' && !emailRegex.test(value)) {
                message = input.dataset.errorMessage || 'Please enter a valid email address.';
            }
        } else if (input.matches('[data-validate-card-number]')) {
            const cleaned = input.value.replace(/\D+/g, '').slice(0, 16);
            input.value = cleaned.replace(/(.{4})/g, '$1 ').trim();
            if (cleaned !== '' && !/^[0-9]{16}$/.test(cleaned)) message = 'Card number must contain 16 digits only.';
        } else if (input.matches('[data-validate-cvc]')) {
            const cleaned = input.value.replace(/\D+/g, '').slice(0, 3);
            input.value = cleaned;
            if (cleaned !== '' && !/^[0-9]{3}$/.test(cleaned)) message = 'CVC must contain 3 digits only.';
        } else if (input.matches('[data-validate-expiry]')) {
            let cleaned = input.value.replace(/[^0-9/]/g, '').slice(0, 5);
            if (/^[0-9]{3,4}$/.test(cleaned)) cleaned = cleaned.slice(0,2) + '/' + cleaned.slice(2);
            input.value = cleaned;
            if (cleaned !== '' && !/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(cleaned)) message = 'Expiry must use MM/YY format.';
        }

        setError(input, message);
        return message === '';
    }

    document.querySelectorAll('.validated-form input, .validated-form textarea').forEach((input) => {
        input.addEventListener('input', () => validateInput(input));
        input.addEventListener('blur', () => validateInput(input));
    });

    document.querySelectorAll('.validated-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const fields = Array.from(form.querySelectorAll('input, textarea')).filter((field) => field.type !== 'hidden' && field.type !== 'file');
            const results = fields.map((field) => validateInput(field));
            const valid = results.every(Boolean);
            if (!valid) {
                event.preventDefault();
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    });
});
