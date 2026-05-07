<?php require 'views/layout/header.php'; ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-500 to-indigo-600 px-6 py-8 text-center">
            <i class="fa-solid fa-lock text-4xl text-white mb-4"></i>
            <h2 class="text-3xl font-extrabold text-white">Mot de passe oublié</h2>
            <p class="mt-2 text-brand-100 text-sm">Récupération de compte Cosmos Beach</p>
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

            <!-- Message de succès -->
            <?php if(!empty($success)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-green-700 font-medium mb-3"><?= htmlspecialchars($success) ?></p>
                            <ol class="text-xs text-green-600 space-y-1 ml-4 list-decimal">
                                <li>Vérifiez votre boîte mail (et dossier spam)</li>
                                <li>Entrez le code reçu</li>
                                <li>Créez un nouveau mot de passe sécurisé</li>
                            </ol>
                            <a href="<?= BASE_URL ?>/?action=verify_otp" class="mt-3 inline-block text-sm font-medium text-green-600 hover:text-green-700 underline">
                                Aller à la vérification →
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Information -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-brand-500 rounded-r-lg">
                    <p class="text-sm text-gray-700">
                        <i class="fa-solid fa-info-circle text-brand-500 mr-2"></i>
                        Entrez l'adresse email de votre compte Cosmos Beach. Nous vous enverrons un code de vérification.
                    </p>
                </div>

                <form class="space-y-6" action="<?= BASE_URL ?>/?action=forgot_password" method="POST">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <!-- Champ Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse e-mail du compte
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                required 
                                class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 px-4 py-3 text-sm border-gray-300 rounded-lg bg-gray-50 border outline-none transition-colors"
                                placeholder="vous@exemple.com"
                            >
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fa-solid fa-shield-check mr-1"></i>
                            Nous vérifierons que ce compte existe
                        </p>
                    </div>

                    <!-- Bouton de soumission -->
                    <div>
                        <button 
                            type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:-translate-y-0.5 active:translate-y-0"
                        >
                            <i class="fa-solid fa-paper-plane mr-2"></i> Envoyer le code
                        </button>
                    </div>
                    
                    <!-- Conseils de sécurité -->
                    <div class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                        <p class="text-xs text-amber-800">
                            <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                            <strong>Conseil:</strong> Un code sécurisé sera envoyé à votre adresse email et expirera après 15 minutes.
                        </p>
                    </div>

                    <!-- Retour à la connexion -->
                    <div class="text-center">
                        <a 
                            href="<?= BASE_URL ?>/?action=login" 
                            class="text-sm font-medium text-brand-600 hover:text-brand-500 underline transition-colors"
                        >
                            <i class="fa-solid fa-arrow-left mr-1"></i>
                            Retourner à la connexion
                        </a>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Procédure en étapes -->
            <?php if(empty($success)): ?>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-xs font-semibold text-gray-600 mb-4 uppercase tracking-wide">
                        <i class="fa-solid fa-stream mr-1"></i>
                        Processus de récupération
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-brand-100 text-brand-600 text-xs font-bold">1</div>
                            <p class="ml-3 text-xs text-gray-600">Entrez votre adresse email</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-brand-100 text-brand-600 text-xs font-bold">2</div>
                            <p class="ml-3 text-xs text-gray-600">Recevez un code par email (15 min)</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-brand-100 text-brand-600 text-xs font-bold">3</div>
                            <p class="ml-3 text-xs text-gray-600">Vérifiez le code (5 tentatives max)</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-brand-100 text-brand-600 text-xs font-bold">4</div>
                            <p class="ml-3 text-xs text-gray-600">Créez un nouveau mot de passe sécurisé</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
