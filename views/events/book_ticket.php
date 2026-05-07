<?php require 'views/layout/header.php'; ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Bouton retour -->
    <a href="<?= BASE_URL ?>/?action=events"
       class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-800 text-sm font-medium mb-6 group">
        <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
        Retour aux événements
    </a>

    <!-- Carte événement -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <?php if ($event['image_url']): ?>
            <div class="h-48 overflow-hidden">
                <img src="<?= htmlspecialchars($event['image_url']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($event['titre']) ?>">
            </div>
        <?php else: ?>
            <div class="h-24 cb-gradient flex items-center justify-center">
                <i class="fa-solid fa-star text-white text-4xl"></i>
            </div>
        <?php endif; ?>
        <div class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($event['titre']) ?></h1>
                    <?php if ($event['lieu']): ?>
                        <p class="text-gray-500 text-sm mt-1"><i class="fa-solid fa-location-dot mr-1 text-brand-500"></i><?= htmlspecialchars($event['lieu']) ?></p>
                    <?php endif; ?>
                </div>
                <span class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                    <?= htmlspecialchars($event['type_event'] ?? 'autre') ?>
                </span>
            </div>
            <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar text-brand-500"></i>
                    Du <strong><?= date('d/m/Y à H:i', strtotime($event['date_debut'])) ?></strong>
                    au <strong><?= date('d/m/Y à H:i', strtotime($event['date_fin'])) ?></strong>
                </div>
            </div>
            <?php if ($event['description']): ?>
                <p class="mt-4 text-gray-700 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulaire de réservation -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-ticket text-brand-500"></i> Réserver vos places
        </h2>

        <?php if ($available <= 0): ?>
            <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <i class="fa-solid fa-circle-xmark text-red-400 text-4xl mb-3"></i>
                <p class="font-semibold text-red-800">Complet !</p>
                <p class="text-red-600 text-sm mt-1">Toutes les places pour cet événement sont épuisées.</p>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-500 mb-6">
                <i class="fa-solid fa-chair text-green-500 mr-1"></i>
                <strong class="text-green-700"><?= $available ?></strong> place(s) disponible(s) sur <?= $event['capacite_max'] ?>
            </p>

            <form action="<?= BASE_URL ?>/?action=book_ticket_event&id=<?= $event['id'] ?>" method="POST" id="bookingForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <!-- Prix par ticket -->
                <div class="bg-brand-50 border border-brand-200 rounded-xl p-4 mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-brand-600 font-medium uppercase tracking-wide">Prix par ticket</p>
                        <p class="text-2xl font-bold text-brand-700"><?= number_format((float)$event['prix_ticket'], 0, ',', ' ') ?> <span class="text-sm font-medium">FC</span></p>
                    </div>
                    <i class="fa-solid fa-tag text-brand-300 text-3xl"></i>
                </div>

                <!-- Nombre de places -->
                <div class="mb-6">
                    <label for="nombre_places" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nombre de places <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeQty(-1)"
                                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition flex items-center justify-center">
                            <i class="fa-solid fa-minus text-xs"></i>
                        </button>
                        <input type="number" id="nombre_places" name="nombre_places" value="1"
                               min="1" max="<?= $available ?>"
                               class="w-24 text-center text-lg font-bold border-2 border-gray-200 rounded-xl py-2 px-3 focus:border-brand-400 outline-none"
                               oninput="updateTotal()">
                        <button type="button" onclick="changeQty(1)"
                                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition flex items-center justify-center">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-gradient-to-r from-brand-600 to-indigo-600 rounded-xl p-5 mb-6 text-white flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-xs font-medium uppercase tracking-wide">Montant total</p>
                        <p class="text-3xl font-bold" id="totalDisplay"><?= number_format((float)$event['prix_ticket'], 0, ',', ' ') ?> FC</p>
                    </div>
                    <i class="fa-solid fa-receipt text-blue-300 text-3xl"></i>
                </div>

                <button type="submit"
                        class="w-full py-3.5 cb-btn-primary rounded-xl font-bold text-base shadow-md flex items-center justify-center gap-2">
                    <i class="fa-solid fa-ticket"></i>
                    Confirmer ma réservation
                </button>
                <p class="text-xs text-gray-400 text-center mt-3">
                    <i class="fa-solid fa-envelope mr-1"></i>
                    Un email de confirmation vous sera envoyé automatiquement.
                </p>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
const prixUnitaire = <?= (float)$event['prix_ticket'] ?>;
const maxPlaces    = <?= $available ?>;

function changeQty(delta) {
    const input = document.getElementById('nombre_places');
    let val = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(maxPlaces, val));
    input.value = val;
    updateTotal();
}

function updateTotal() {
    const input = document.getElementById('nombre_places');
    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) { val = 1; input.value = 1; }
    if (val > maxPlaces) { val = maxPlaces; input.value = maxPlaces; }
    const total = val * prixUnitaire;
    document.getElementById('totalDisplay').textContent =
        total.toLocaleString('fr-FR', {minimumFractionDigits: 0}) + ' FC';
}
</script>

<?php require 'views/layout/footer.php'; ?>
