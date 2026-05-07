<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-6 py-8 sm:p-10">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Recherche des Paiements Encaissés</h1>
                    <p class="mt-2 text-purple-100">
                        Visualisez et recherchez tous les paiements traités
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200">
                        <i class="fa-solid fa-credit-card mr-2"></i> Paiements: <strong class="ml-2"><?= $total ?? 0 ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Filtres de recherche -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fa-solid fa-filter mr-2"></i>Filtres
                </h3>
                <form method="POST" action="<?= BASE_URL ?>/?action=search_payments" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="reservation_id" class="block text-sm font-medium text-gray-700 mb-1">Numéro Réservation</label>
                        <input type="number" id="reservation_id" name="reservation_id" placeholder="Ex: 1" 
                               value="<?= isset($filters['reservation_id']) ? htmlspecialchars($filters['reservation_id']) : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Client</label>
                        <input type="text" id="client_name" name="client_name" placeholder="Ex: Dupont" 
                               value="<?= isset($filters['client_name']) ? htmlspecialchars($filters['client_name']) : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label for="methode" class="block text-sm font-medium text-gray-700 mb-1">Méthode</label>
                        <select id="methode" name="methode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                            <option value="">-- Toutes les méthodes --</option>
                            <option value="ESPECES" <?= isset($filters['methode']) && $filters['methode'] === 'ESPECES' ? 'selected' : '' ?>>Espèces</option>
                            <option value="CARTE" <?= isset($filters['methode']) && $filters['methode'] === 'CARTE' ? 'selected' : '' ?>>Carte</option>
                            <option value="MOBILE_MONEY" <?= isset($filters['methode']) && $filters['methode'] === 'MOBILE_MONEY' ? 'selected' : '' ?>>Mobile Money</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                        <input type="date" id="date_from" name="date_from" 
                               value="<?= isset($filters['date_from']) ? htmlspecialchars($filters['date_from']) : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                        <input type="date" id="date_to" name="date_to" 
                               value="<?= isset($filters['date_to']) ? htmlspecialchars($filters['date_to']) : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                    </div>

                    <div class="flex items-end gap-3">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fa-solid fa-search mr-2"></i>Rechercher
                        </button>
                        <a href="<?= BASE_URL ?>/?action=search_payments" class="inline-flex justify-center items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors">
                            <i class="fa-solid fa-rotate-right mr-2"></i>Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tableau des paiements -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-hashtag mr-2"></i>ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-user mr-2"></i>Client
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-calendar-days mr-2"></i>Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-circle-dollar mr-2"></i>Montant
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-credit-card mr-2"></i>Méthode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-person mr-2"></i>Caissier
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($payments)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 font-medium">Aucun paiement trouvé</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($payments as $payment): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-600">
                                        #<?= $payment['id'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($payment['client_prenom'] . ' ' . $payment['client_nom']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500">Rés. #<?= $payment['reservation_id'] ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <i class="fa-solid fa-calendar-days mr-2 text-gray-400"></i>
                                        <?= date('d/m/Y H:i', strtotime($payment['date_paiement'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                        <?= number_format($payment['montant'], 2, ',', ' ') ?> FC
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php 
                                        $badge_class = 'bg-blue-100 text-blue-800';
                                        if ($payment['methode'] === 'ESPECES') {
                                            $badge_class = 'bg-green-100 text-green-800';
                                        } elseif ($payment['methode'] === 'CARTE') {
                                            $badge_class = 'bg-purple-100 text-purple-800';
                                        } elseif ($payment['methode'] === 'MOBILE_MONEY') {
                                            $badge_class = 'bg-orange-100 text-orange-800';
                                        }
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $badge_class ?>">
                                            <?php 
                                            $icons = [
                                                'ESPECES' => 'fa-money-bill',
                                                'CARTE' => 'fa-credit-card',
                                                'MOBILE_MONEY' => 'fa-mobile'
                                            ];
                                            echo '<i class="fa-solid ' . ($icons[$payment['methode']] ?? 'fa-question') . ' mr-1"></i>';
                                            echo htmlspecialchars($payment['methode']);
                                            ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if($payment['caissier_nom']): ?>
                                            <div class="flex flex-col">
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($payment['caissier_prenom'] . ' ' . $payment['caissier_nom']) ?>
                                                </p>
                                                <p class="text-xs text-gray-500">Caissier</p>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?= BASE_URL ?>/?action=receipt&id=<?= $payment['reservation_id'] ?>" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors border border-blue-200">
                                            <i class="fa-solid fa-file-pdf mr-1"></i>Reçu
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Affichage <strong><?= ($offset + 1) ?></strong> à <strong><?= min($offset + $limit, $total) ?></strong> sur <strong><?= $total ?></strong> paiements
                    </p>
                    <div class="flex gap-2">
                        <?php if($page > 1): ?>
                            <a href="<?= BASE_URL ?>/?action=search_payments&page=<?= $page - 1 ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                                <i class="fa-solid fa-chevron-left mr-1"></i>Précédent
                            </a>
                        <?php endif; ?>

                        <?php 
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        if($start > 1): ?>
                            <a href="<?= BASE_URL ?>/?action=search_payments&page=1" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors">1</a>
                            <?php if($start > 2): ?>
                                <span class="px-3 py-2 text-gray-500">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i === $page): ?>
                                <span class="px-3 py-2 bg-purple-600 text-white rounded-lg font-medium"><?= $i ?></span>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/?action=search_payments&page=<?= $i ?>" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total_pages): ?>
                            <?php if($end < $total_pages - 1): ?>
                                <span class="px-3 py-2 text-gray-500">...</span>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/?action=search_payments&page=<?= $total_pages ?>" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors">
                                <?= $total_pages ?>
                            </a>
                        <?php endif; ?>

                        <?php if($page < $total_pages): ?>
                            <a href="<?= BASE_URL ?>/?action=search_payments&page=<?= $page + 1 ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                                Suivant<i class="fa-solid fa-chevron-right ml-1"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
