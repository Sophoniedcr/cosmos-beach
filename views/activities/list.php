<?php require 'views/layout/header.php'; ?>

<div class="bg-white">
    <!-- Header Section -->
    <div class="bg-brand-600 py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl tracking-tight">
                Nos Activités & Services
            </h1>
            <p class="mt-4 max-w-2xl text-xl text-brand-100 mx-auto">
                De la détente en VIP aux aventures en famille, réservez en ligne pour garantir votre place.
            </p>
        </div>
    </div>

    <!-- Filtres (Simples pour l'instant) -->
    <div class="border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <?= count($activities) ?> activité(s) disponible(s)
            </div>
            <!-- Vous pourriez rajouter des boutons de filtre par Type ici -->
        </div>
    </div>

    <!-- Liste des Activités -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <?php if(empty($activities)): ?>
            <div class="text-center py-12">
                <i class="fa-regular fa-folder-open text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900">Aucune activité pour le moment</h3>
                <p class="text-gray-500 mt-2">Revenez bientôt ou contactez la réception.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
                <?php foreach($activities as $activity): ?>
                    <div class="group relative flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                        <div class="aspect-w-3 aspect-h-2 bg-gray-200 overflow-hidden group-hover:opacity-75 sm:aspect-w-4 sm:aspect-h-3 h-56 relative">
                            <?php if($activity['image_url']): ?>
                                <img src="<?= htmlspecialchars($activity['image_url']) ?>" alt="<?= htmlspecialchars($activity['nom']) ?>" class="w-full h-full object-center object-cover transition-transform duration-500 group-hover:scale-105">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 fill-gray-400 text-6xl">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-semibold shadow-sm text-gray-800">
                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $activity['type']))) ?>
                            </div>
                        </div>
                        <div class="flex-1 p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    <a href="<?= BASE_URL ?>/?action=activity_details&id=<?= $activity['id'] ?>">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        <?= htmlspecialchars($activity['nom']) ?>
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500 mb-4 line-clamp-3">
                                    <?= htmlspecialchars($activity['description']) ?>
                                </p>
                            </div>
                            <div class="border-t border-gray-100 pt-4 mt-4 flex items-center justify-between">
                                <p class="text-lg font-bold text-brand-600">
                                    <?= number_format($activity['prix'], 2, ',', ' ') ?> FC
                                </p>
                                <div class="text-sm text-gray-500 flex items-center">
                                    <i class="fa-regular fa-clock mr-1"></i> <?= htmlspecialchars($activity['duree']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
