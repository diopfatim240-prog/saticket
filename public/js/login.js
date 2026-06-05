document.addEventListener("DOMContentLoaded", function() {
    // 1. Récupération des éléments
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const btnSubmit = document.getElementById('btnSubmit');
    const loginForm = document.getElementById('loginForm');

    // 2. Initialisation du bouton
    // Ne pas bloquer l'accès si le JavaScript ne se charge pas correctement
    if (btnSubmit) {
        btnSubmit.disabled = false;
    }

    // 3. Fonction d'affichage du message
    function showError(input, message) {
        if (!input) return;
        let errorDisplay = input.parentNode.querySelector('.error-msg');
        if (!errorDisplay) {
            errorDisplay = document.createElement('small');
            errorDisplay.className = 'error-msg';
            errorDisplay.style.display = 'block';
            errorDisplay.style.color = 'red';
            input.parentNode.appendChild(errorDisplay);
        }
        errorDisplay.textContent = message ? message : "";
    }

    // 4. Fonction de vérification globale (Unique et propre)
    function updateButtonState() {
        if (!btnSubmit || !emailInput || !passwordInput) return;
        
        const emailRes = Validator.emailValidator("L'email", emailInput.value.trim());
        const passRes = Validator.passwordValidator("Le mot de passe", passwordInput.value.trim(), 8);
        
        // On active si AUCUNE erreur n'est présente
        btnSubmit.disabled = (emailRes.error || passRes.error);
    }

    // 5. Événements Email
    if (emailInput) {
        emailInput.addEventListener('input', () => {
            const result = Validator.emailValidator("L'email", emailInput.value.trim());
            showError(emailInput, result.error ? result.message : "");
            updateButtonState();
        });
    }

    // 6. Événements Mot de passe
    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            const result = Validator.passwordValidator("Le mot de passe", passwordInput.value.trim(), 8);
            showError(passwordInput, result.error ? result.message : "");
            updateButtonState();
        });
    }

   
});