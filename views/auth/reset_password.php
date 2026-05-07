<?php require 'views/layout/header.php'; ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-600 to-indigo-600 px-6 py-8 text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-white text-brand-600 mb-4">
                <i class="fa-solid fa-lock-open text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-white">Nouveau mot de passe</h2>
            <p class="mt-2 text-brand-100 text-sm">Créez un mot de passe sécurisé</p>
        </div>
        
        <div class="p-8">
            <!-- Messages d'erreur -->
            <?php if(!empty($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 relative">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-exclamation text-red-500 h-5 w-5"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Message de succès -->
            <?php if(!empty($success)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 relative">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-check-circle text-green-500 h-5 w-5"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700"><?= htmlspecialchars($success) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?= BASE_URL ?>/?action=reset_password" method="POST" id="resetForm">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <!-- Nouveau mot de passe -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nouveau mot de passe
                    </label>
                    <div class="relative">
                        <input 
                            id="new_password" 
                            name="new_password" 
                            type="password" 
                            autocomplete="new-password"
                            required 
                            class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 px-4 py-3 border-gray-300 rounded-lg bg-gray-50 border outline-none transition-colors"
                            placeholder="••••••••"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePasswordVisibility('new_password', 'toggleIcon1')"
                        >
                            <i id="toggleIcon1" class="fa-solid fa-eye text-gray-400 hover:text-gray-600 cursor-pointer"></i>
                        </button>
                    </div>
                    <div class="mt-2 password-strength">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs text-gray-600">Force du mot de passe</label>
                            <span id="strength-text" class="text-xs font-medium">Faible</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="strength-bar" class="bg-red-500 h-2 rounded-full w-1/4 transition-all"></div>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Minimum 8 caractères, avec lettres, chiffres et caractères spéciaux.
                    </p>
                </div>

                <!-- Confirmer mot de passe -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmer le mot de passe
                    </label>
                    <div class="relative">
                        <input 
                            id="confirm_password" 
                            name="confirm_password" 
                            type="password" 
                            autocomplete="new-password"
                            required 
                            class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 px-4 py-3 border-gray-300 rounded-lg bg-gray-50 border outline-none transition-colors"
                            placeholder="••••••••"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-check text-gray-400"></i>
                        </div>
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePasswordVisibility('confirm_password', 'toggleIcon2')"
                        >
                            <i id="toggleIcon2" class="fa-solid fa-eye text-gray-400 hover:text-gray-600 cursor-pointer"></i>
                        </button>
                    </div>
                    <div id="match-status" class="mt-2 text-xs text-gray-500 flex items-center">
                        <i class="fa-solid fa-circle text-gray-300 mr-2"></i>
                        <span>Les mots de passe doivent correspondre</span>
                    </div>
                </div>

                <!-- Critères de mot de passe -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-3">Critères de sécurité:</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm">
                            <i id="check-length" class="fa-solid fa-circle text-gray-300 mr-2 text-xs"></i>
                            <span id="text-length">Au moins 8 caractères</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <i id="check-upper" class="fa-solid fa-circle text-gray-300 mr-2 text-xs"></i>
                            <span>Une lettre majuscule</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <i id="check-lower" class="fa-solid fa-circle text-gray-300 mr-2 text-xs"></i>
                            <span>Une lettre minuscule</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <i id="check-number" class="fa-solid fa-circle text-gray-300 mr-2 text-xs"></i>
                            <span>Un chiffre</span>
                        </li>
                    </ul>
                </div>

                <!-- Bouton de soumission -->
                <div>
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fa-solid fa-shield-check mr-2"></i> Réinitialiser le mot de passe
                    </button>
                </div>

                <!-- Retour à la connexion -->
                <div class="text-center">
                    <a 
                        href="<?= BASE_URL ?>/?action=login" 
                        class="text-sm font-medium text-brand-600 hover:text-brand-500 underline transition-colors"
                    >
                        Retourner à la connexion
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Affichage/masquage des mots de passe
    function togglePasswordVisibility(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Validation du mot de passe en temps réel
    const newPasswordField = document.getElementById('new_password');
    const confirmPasswordField = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');

    function validatePassword(password) {
        const checks = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        return checks;
    }

    function updatePasswordStrength(password) {
        const checks = validatePassword(password);
        const checkCount = Object.values(checks).filter(Boolean).length;
        
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        
        let strength = 'Faible';
        let color = 'bg-red-500';
        let width = '25%';

        if (checkCount === 2) {
            strength = 'Moyen';
            color = 'bg-yellow-500';
            width = '50%';
        } else if (checkCount === 3) {
            strength = 'Bon';
            color = 'bg-blue-500';
            width = '75%';
        } else if (checkCount === 4) {
            strength = 'Excellent';
            color = 'bg-green-500';
            width = '100%';
        }

        strengthBar.className = `${color} h-2 rounded-full transition-all`;
        strengthBar.style.width = width;
        strengthText.textContent = strength;

        // Mettre à jour les icônes de critères
        document.getElementById('check-length').className = checks.length 
            ? 'fa-solid fa-check-circle text-green-500 mr-2 text-xs' 
            : 'fa-solid fa-circle text-gray-300 mr-2 text-xs';
        
        document.getElementById('check-upper').className = checks.upper 
            ? 'fa-solid fa-check-circle text-green-500 mr-2 text-xs' 
            : 'fa-solid fa-circle text-gray-300 mr-2 text-xs';
        
        document.getElementById('check-lower').className = checks.lower 
            ? 'fa-solid fa-check-circle text-green-500 mr-2 text-xs' 
            : 'fa-solid fa-circle text-gray-300 mr-2 text-xs';
        
        document.getElementById('check-number').className = checks.number 
            ? 'fa-solid fa-check-circle text-green-500 mr-2 text-xs' 
            : 'fa-solid fa-circle text-gray-300 mr-2 text-xs';

        validateForm();
    }

    function validatePasswordMatch() {
        const match = newPasswordField.value === confirmPasswordField.value;
        const matchStatus = document.getElementById('match-status');
        
        if (newPasswordField.value.length === 0 && confirmPasswordField.value.length === 0) {
            matchStatus.innerHTML = '<i class="fa-solid fa-circle text-gray-300 mr-2"></i><span>Les mots de passe doivent correspondre</span>';
            matchStatus.className = 'mt-2 text-xs text-gray-500 flex items-center';
        } else if (match) {
            matchStatus.innerHTML = '<i class="fa-solid fa-check-circle text-green-500 mr-2"></i><span class="text-green-600">Les mots de passe correspondent</span>';
            matchStatus.className = 'mt-2 text-xs text-green-600 flex items-center';
        } else {
            matchStatus.innerHTML = '<i class="fa-solid fa-times-circle text-red-500 mr-2"></i><span class="text-red-600">Les mots de passe ne correspondent pas</span>';
            matchStatus.className = 'mt-2 text-xs text-red-600 flex items-center';
        }

        validateForm();
    }

    function validateForm() {
        const password = newPasswordField.value;
        const confirmPassword = confirmPasswordField.value;
        const checks = validatePassword(password);
        
        const isValid = 
            checks.length && 
            checks.upper && 
            checks.lower && 
            checks.number && 
            password === confirmPassword &&
            password.length > 0;

        submitBtn.disabled = !isValid;
    }

    // Event listeners
    newPasswordField.addEventListener('input', function() {
        updatePasswordStrength(this.value);
        validatePasswordMatch();
    });

    confirmPasswordField.addEventListener('input', validatePasswordMatch);

    // Validation initiale
    validateForm();
</script>

<?php require 'views/layout/footer.php'; ?>
