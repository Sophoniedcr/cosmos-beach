<?php require 'views/layout/header.php'; ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-500 to-indigo-600 px-6 py-8 text-center">
            <i class="fa-solid fa-mobile text-4xl text-white mb-4"></i>
            <h2 class="text-3xl font-extrabold text-white">Vérification</h2>
            <p class="mt-2 text-brand-100 text-sm">Entrez le code reçu par email</p>
        </div>
        
        <div class="p-8">
            <!-- Messages d'erreur -->
            <?php if(!empty($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 relative">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-exclamation text-red-500 mr-3"></i>
                        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Informations -->
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-brand-500 rounded-r-lg">
                <p class="text-sm text-gray-700">
                    <i class="fa-solid fa-info-circle text-brand-500 mr-2"></i>
                    Un code de 6 chiffres a été envoyé à <strong><?= substr($_SESSION['reset_email'] ?? '', 0, 3) ?>***@***</strong>
                </p>
            </div>

            <form class="space-y-6" action="<?= BASE_URL ?>/?action=verify_otp" method="POST">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <!-- Champ OTP -->
                <div>
                    <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Code
                    </label>
                    <div class="relative">
                        <input 
                            id="otp_code" 
                            name="otp_code" 
                            type="text" 
                            inputmode="numeric"
                            pattern="\d{6}"
                            maxlength="6"
                            autocomplete="off"
                            required 
                            class="focus:ring-brand-500 focus:border-brand-500 block w-full px-4 py-3 text-center text-2xl tracking-widest border-gray-300 rounded-lg bg-gray-50 border outline-none transition-colors font-mono"
                            placeholder="000000"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-keyboard text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fa-solid fa-clock mr-1"></i>
                        Le code expire dans 5 minutes
                    </p>
                </div>

                <!-- Bouton de soumission -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:-translate-y-0.5 active:translate-y-0"
                    >
                        <i class="fa-solid fa-check-circle mr-2"></i> Vérifier le code
                    </button>
                </div>

                <!-- Conseils de sécurité -->
                <div class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                    <p class="text-xs text-amber-800">
                        <i class="fa-solid fa-shield-exclamation mr-1"></i>
                        <strong>Conseil:</strong> Ne partagez jamais votre code avec quiconque.
                    </p>
                </div>

                <!-- Lien pour recommencer -->
                <div class="text-center pt-4">
                    <p class="text-sm text-gray-600 mb-2">Vous n'avez pas reçu le code?</p>
                    <a 
                        href="<?= BASE_URL ?>/?action=forgot_password" 
                        class="text-sm font-medium text-brand-600 hover:text-brand-500 underline transition-colors"
                    >
                        Demander un nouveau code
                    </a>
                </div>
            </form>

            <!-- Zone de debug (developpement uniquement) -->
            <?php if(isset($_SESSION['debug_otp']) && isset($_GET['debug'])): ?>
                <div class="mt-6 p-3 bg-gray-100 border border-gray-300 rounded text-center">
                    <p class="text-xs text-gray-600">
                        <strong>Mode Développement:</strong> Code = <span class="font-mono font-bold"><?= $_SESSION['debug_otp'] ?></span>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Validation et formatage du code OTP
    document.getElementById('otp_code').addEventListener('input', function(e) {
        // Accepter uniquement les chiffres
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Limiter à 6 caractères
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
        
        // Auto-submit si 6 chiffres sont entrés (optionnel)
        // Décommentez la ligne suivante pour activer l'auto-soumission
        // if (this.value.length === 6) {
        //     this.form.submit();
        // }
    });

    // Accepter seulement les chiffres
    document.getElementById('otp_code').addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });
</script>

<?php require 'views/layout/footer.php'; ?>
