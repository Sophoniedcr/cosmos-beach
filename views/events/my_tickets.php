<?php require 'views/layout/header.php'; ?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-ticket cb-gradient-text"></i> Mes Tickets d'Événements
            </h1>
            <p class="text-gray-500 text-sm mt-1">Retrouvez tous vos tickets de réservation</p>
        </div>
        <a href="<?= BASE_URL ?>/?action=events"
           class="inline-flex items-center gap-2 px-4 py-2.5 cb-btn-primary rounded-xl text-sm font-semibold shadow">
            <i class="fa-solid fa-calendar-star"></i> Voir les événements
        </a>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fa-solid fa-ticket text-3xl text-brand-400"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Aucun ticket pour le moment</h2>
            <p class="text-gray-500 text-sm mb-6">Réservez un ticket pour un événement Cosmos Beach !</p>
            <a href="<?= BASE_URL ?>/?action=events"
               class="inline-flex items-center gap-2 px-6 py-3 cb-btn-primary rounded-xl font-semibold shadow">
                <i class="fa-solid fa-star"></i> Découvrir les événements
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($tickets as $t): ?>
                <?php
                    $statutColor = match($t['statut']) {
                        'CONFIRME'   => 'green',
                        'ANNULE'     => 'red',
                        default      => 'yellow',
                    };
                    $statutLabel = match($t['statut']) {
                        'CONFIRME'   => '✅ Confirmé',
                        'ANNULE'     => '❌ Annulé',
                        default      => '⏳ En attente',
                    };
                ?>
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row">
                        <!-- Image -->
                        <div class="sm:w-40 h-32 sm:h-auto flex-shrink-0 overflow-hidden">
                            <?php if ($t['image_url']): ?>
                                <img src="<?= htmlspecialchars($t['image_url']) ?>" class="w-full h-full object-cover" alt="">
                            <?php else: ?>
                                <div class="w-full h-full cb-gradient flex items-center justify-center">
                                    <i class="fa-solid fa-star text-white text-3xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Contenu -->
                        <div class="flex-1 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <!-- Numéro de ticket -->
                                <div class="inline-flex items-center gap-1.5 bg-brand-50 border border-brand-200 px-3 py-1 rounded-full text-brand-700 text-xs font-bold mb-2 tracking-widest">
                                    <i class="fa-solid fa-barcode text-xs"></i>
                                    <?= htmlspecialchars($t['numero_ticket']) ?>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($t['event_titre']) ?></h3>
                                <div class="mt-1.5 flex flex-wrap gap-3 text-xs text-gray-500">
                                    <?php if ($t['lieu']): ?>
                                        <span><i class="fa-solid fa-location-dot mr-1 text-brand-400"></i><?= htmlspecialchars($t['lieu']) ?></span>
                                    <?php endif; ?>
                                    <span><i class="fa-regular fa-calendar mr-1 text-brand-400"></i>
                                        <?= date('d/m/Y', strtotime($t['date_debut'])) ?> → <?= date('d/m/Y', strtotime($t['date_fin'])) ?>
                                    </span>
                                    <span><i class="fa-solid fa-users mr-1 text-brand-400"></i><?= $t['nombre_places'] ?> place(s)</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">
                                    Acheté le <?= date('d/m/Y à H:i', strtotime($t['date_achat'])) ?>
                                </p>
                            </div>

                            <!-- Prix + Statut -->
                            <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 flex-shrink-0">
                                <p class="text-xl font-bold text-brand-700">
                                    <?= number_format((float)$t['montant_total'], 0, ',', ' ') ?> FC
                                </p>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    bg-<?= $statutColor ?>-100 text-<?= $statutColor ?>-800 border border-<?= $statutColor ?>-200">
                                    <?= $statutLabel ?>
                                </span>
                                <?php if ($t['statut'] === 'EN_ATTENTE'): ?>
                                    <a href="<?= BASE_URL ?>/?action=cancel_event_ticket&id=<?= $t['id'] ?>"
                                       onclick="return confirm('Annuler ce ticket ?')"
                                       class="text-xs text-red-500 hover:text-red-700 font-medium transition">
                                        <i class="fa-solid fa-xmark mr-1"></i>Annuler
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require 'views/layout/footer.php'; ?>
