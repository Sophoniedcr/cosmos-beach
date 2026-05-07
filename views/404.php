<?php require 'views/layout/header.php'; ?>
<div class="min-h-[60vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h2 class="block text-9xl font-extrabold text-brand-500 drop-shadow-sm">404</h2>
            <h1 class="mt-6 text-3xl font-bold text-gray-900">Page Introuvable</h1>
            <p class="mt-4 text-gray-500 text-lg">
                Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
            </p>
        </div>
        <div class="mt-8">
            <a href="<?= BASE_URL ?>/" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition-all hover:-translate-y-1">
                <i class="fa-solid fa-house mr-2"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>
<?php require 'views/layout/footer.php'; ?>
