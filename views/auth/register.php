<?php require 'views/layout/header.php'; ?>

<div class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col">
        <!-- Partie supérieure design -->
        <div class="bg-gradient-to-r from-brand-600 to-indigo-600 p-8 text-center">
            <h2 class="text-3xl font-extrabold text-white">Rejoignez-nous</h2>
            <p class="text-brand-100 mt-2">Créez votre compte Cosmos Beach gratuit.</p>
        </div>

        <!-- Formulaire -->
        <div class="p-8">
            <?php if(!empty($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-exclamation text-red-500 mr-2"></i>
                        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(!empty($success)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fa-solid fa-circle-check text-green-500 mr-2"></i>
                            <p class="text-sm text-green-700"><?= htmlspecialchars($success) ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/?action=login" class="text-sm font-medium text-green-800 hover:text-green-900 underline">Me connecter</a>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?= BASE_URL ?>/?action=register" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="space-y-4">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom complet</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user text-gray-400"></i>
                            </div>
                            <input id="nom" name="nom" type="text" required class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-50 border outline-none" placeholder="Jean Dupont">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Adresse Email</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-50 border outline-none" placeholder="vous@exemple.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required minlength="6" class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-50 border outline-none" placeholder="••••••••">
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input id="confirm_password" name="confirm_password" type="password" required minlength="6" class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-50 border outline-none" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-transform transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-user-plus mr-2 my-auto"></i> S'inscrire
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Vous avez déjà un compte ? 
                    <a href="<?= BASE_URL ?>/?action=login" class="font-medium text-brand-600 hover:text-brand-500">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
