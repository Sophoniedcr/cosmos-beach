<?php
// Empêcher une double inclusion du header si déjà démarré
if (!headers_sent()) {
    http_response_code(404);
}
?>
<?php require 'views/layout/header.php'; ?>

<div class="min-h-[70vh] flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full text-center space-y-6">

        <div class="text-9xl font-extrabold text-sky-500 drop-shadow-sm leading-none">404</div>

        <div>
            <h1 class="text-3xl font-bold text-gray-900">Page introuvable</h1>
            <p class="mt-3 text-gray-500 text-base">
                La page que vous cherchez n'existe pas ou a été déplacée.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-6">
            <a href="<?= BASE_URL ?>/"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl shadow transition">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
            <a href="javascript:history.back()"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white hover:bg-gray-100 text-gray-700 font-semibold rounded-xl shadow border border-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i> Retour
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
