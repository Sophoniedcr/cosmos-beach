<?php
if (!headers_sent()) {
    http_response_code(500);
}
?>
<?php require 'views/layout/header.php'; ?>

<div class="min-h-[70vh] flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full text-center space-y-6">

        <div class="text-9xl font-extrabold text-orange-500 drop-shadow-sm leading-none">500</div>

        <div>
            <h1 class="text-3xl font-bold text-gray-900">Erreur serveur</h1>
            <p class="mt-3 text-gray-500 text-base">
                Une erreur interne s'est produite. Notre équipe a été notifiée.
                Veuillez réessayer dans quelques instants.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-6">
            <a href="<?= BASE_URL ?>/"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl shadow transition">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
            <a href="javascript:location.reload()"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white hover:bg-gray-100 text-gray-700 font-semibold rounded-xl shadow border border-gray-200 transition">
                <i class="fa-solid fa-rotate-right"></i> Réessayer
            </a>
        </div>

    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
