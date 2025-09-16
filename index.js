document.addEventListener('DOMContentLoaded', () => {
    // Get form and input elements
    const form = document.getElementById('signup');
    const username = document.getElementById('name');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const password2 = document.getElementById('password_confirmation');

    // Error handling functions
    function setError(element, message) {
        let errorDisplay = element.parentElement.querySelector('.error');
        if (!errorDisplay) {
            errorDisplay = document.createElement('div');
            errorDisplay.className = 'error';
            element.parentElement.appendChild(errorDisplay);
        }
        errorDisplay.innerText = message;
    }

    function setSuccess(element) {
        let errorDisplay = element.parentElement.querySelector('.error');
        if (errorDisplay) errorDisplay.innerText = '';
    }

    // Input validation
    function validateInputs() {
        let valid = true;
        if (username.value.trim() === '') { setError(username, 'Username required'); valid = false; } else setSuccess(username);
        if (email.value.trim() === '') { setError(email, 'Email required'); valid = false; } else setSuccess(email);
        if (password.value.trim() === '') { setError(password, 'Password required'); valid = false; } else setSuccess(password);
        if (password2.value.trim() === '') { setError(password2, 'Confirm password'); valid = false; } else setSuccess(password2);
        if (password.value && password2.value && password.value !== password2.value) { setError(password2, 'Passwords do not match'); valid = false; }
        return valid;
    }

    // Form submission
    form.addEventListener('submit', async e => {
        e.preventDefault();
        if (!validateInputs()) return;

        const formData = new FormData(form);

        try {
            const response = await fetch('process-signup.php', {
                method: 'POST',
                body: formData
            });

            const raw = await response.text();
            console.log("Raw response from PHP:", raw);

            let result;
            try {
                result = JSON.parse(raw);
            } catch (jsonErr) {
                console.error("Response was not valid JSON:", jsonErr);
                return;
            }

            if (result.success) {
                setSuccess(username);
                setSuccess(email);
                setSuccess(password);
                setSuccess(password2);
                form.reset();
                window.location.href = "signup-success.html";
            } else if (result.field && result.message) {
                const field = document.getElementById(result.field);
                if (field) setError(field, result.message);
                else console.warn("Field not found in the DOM:", result.field);
            } else if (result.message) {
                console.warn("Server message:", result.message);
            }
        } catch (err) {
            console.error('Fetch error:', err);
        }
    });
});
