<?php
if (!headers_sent()) {
    http_response_code(403);
}
?>
<?php require 'views/layout/header.php'; ?>

<div class="min-h-[70vh] flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full text-center space-y-6">

        <div class="text-9xl font-extrabold text-red-500 drop-shadow-sm leading-none">403</div>

        <div>
            <h1 class="text-3xl font-bold text-gray-900">Accès refusé</h1>
            <p class="mt-3 text-gray-500 text-base">
                Vous n'avez pas les droits nécessaires pour accéder à cette page.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-6">
            <a href="<?= BASE_URL ?>/"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow transition">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>/?action=dashboard"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition">
                <i class="fa-solid fa-gauge"></i> Mon espace
            </a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
