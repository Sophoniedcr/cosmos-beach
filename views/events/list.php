<?php require 'views/layout/header.php'; ?>

<div class="bg-slate-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header section -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">Événements & Promotions</h1>
            <p class="mt-4 max-w-2xl text-lg text-gray-500 mx-auto">
                Ne manquez rien de l'actualité de Cosmos Beach. Concerts, soirées à thème ou réductions spéciales !
            </p>
        </div>

        <?php if (empty($events)): ?>
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
                <i class="fa-solid fa-champagne-glasses text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900">Pas d'événement pour le moment</h3>
                <p class="text-gray-500 mt-2">Revenez plus tard pour découvrir nos futures offres !</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($events as $e): ?>
                    <?php
                        $disponibles = max(0, (int)$e['capacite_max'] - (int)($e['tickets_vendus'] ?? 0));
                        $complet = $disponibles <= 0;
                    ?>
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col md:flex-row border border-gray-100 group">
                        <!-- Image -->
                        <div class="md:w-2/5 h-48 md:h-auto relative flex-shrink-0 overflow-hidden">
                            <?php if ($e['image_url']): ?>
                                <img src="<?= htmlspecialchars($e['image_url']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="<?= htmlspecialchars($e['titre']) ?>">
                            <?php else: ?>
                                <div class="w-full h-full cb-gradient flex items-center justify-center">
                                    <i class="fa-solid fa-star text-white text-5xl"></i>
                                </div>
                            <?php endif; ?>
                            <?php if ($complet): ?>
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                    <span class="bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full">COMPLET</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Contenu -->
                        <div class="p-6 md:w-3/5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center text-sm text-brand-600 font-semibold">
                                        <i class="fa-regular fa-clock mr-2"></i>
                                        Du <?= date('d/m', strtotime($e['date_debut'])) ?> au <?= date('d/m/Y', strtotime($e['date_fin'])) ?>
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-brand-600 font-medium border border-blue-100">
                                        <?= htmlspecialchars($e['type_event'] ?? 'autre') ?>
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($e['titre']) ?></h3>
                                <?php if ($e['lieu']): ?>
                                    <p class="text-xs text-gray-500 mb-2"><i class="fa-solid fa-location-dot mr-1 text-brand-400"></i><?= htmlspecialchars($e['lieu']) ?></p>
                                <?php endif; ?>
                                <p class="text-gray-600 text-sm line-clamp-2"><?= nl2br(htmlspecialchars($e['description'] ?? '')) ?></p>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-2xl font-bold text-brand-700"><?= number_format((float)$e['prix_ticket'], 0, ',', ' ') ?> <span class="text-sm font-medium text-gray-500">FC / ticket</span></p>
                                    <?php if (!$complet): ?>
                                        <p class="text-xs text-green-600 font-medium"><i class="fa-solid fa-chair mr-1"></i><?= $disponibles ?> place(s) dispo</p>
                                    <?php endif; ?>
                                </div>
                                <?php if (!$complet): ?>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="<?= BASE_URL ?>/?action=book_ticket_event&id=<?= $e['id'] ?>"
                                           class="inline-flex items-center gap-2 px-4 py-2.5 cb-btn-primary rounded-xl font-semibold text-sm shadow">
                                            <i class="fa-solid fa-ticket"></i> Réserver
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/?action=login"
                                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl font-semibold text-sm transition">
                                            <i class="fa-solid fa-lock"></i> Connexion requise
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 rounded-xl font-semibold text-sm border border-red-200">
                                        <i class="fa-solid fa-xmark"></i> Complet
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
