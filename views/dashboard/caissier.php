<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Dashboard Caissier -->
        <div class="bg-gradient-to-r from-green-600 to-teal-700 px-6 py-8 sm:p-10">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Module Caisse & Facturation</h1>
                    <p class="mt-2 text-green-100">
                        Caissier connecté : <span class="font-semibold"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                        <i class="fa-solid fa-cash-register mr-2"></i> Mode Caisse
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Bouton Historique -->
            <div class="mb-6 flex gap-3 justify-end">
              <a href="<?= BASE_URL ?>/?action=mon_historique"
                 class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl shadow transition text-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Mon Historique Caisse
              </a>
              <a href="<?= BASE_URL ?>/?action=search_payments"
                 class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-green-700 border border-green-300 font-semibold rounded-xl shadow transition text-sm">
                <i class="fa-solid fa-magnifying-glass"></i> Rechercher Paiements
              </a>
            </div>
            <!-- Messages d'erreur/succès -->
            <?php if(!empty($error_message)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-600 mt-1"></i>
                    <span class="text-red-700"><?= htmlspecialchars($error_message) ?></span>
                </div>
            <?php endif; ?>

            <!-- Recherche Réservation (Conformément au diagramme d'activité de la caisse) -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rechercher une réservation</h3>
                <form action="<?= BASE_URL ?>/?action=cashier_search" method="POST" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label for="reservation_id" class="block text-sm font-medium text-gray-700">Numéro de réservation</label>
                        <input type="number" id="reservation_id" name="reservation_id" value="<?= htmlspecialchars($search_id) ?>" required class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3 border outline-none">
                    </div>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fa-solid fa-magnifying-glass mr-2 my-auto"></i> Rechercher
                    </button>
                </form>
            </div>

            <!-- Résultat de recherche -->
            <?php if($search_result): ?>
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-200 mb-8">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">Résultat de la recherche</h3>
                    <div class="bg-white rounded-lg p-4 border border-blue-200">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Réservation</p>
                                <p class="text-lg font-bold text-gray-900">#<?= htmlspecialchars($search_result['id']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Client</p>
                                <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($search_result['client_nom']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Activité</p>
                                <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($search_result['activite_nom']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Montant</p>
                                <p class="text-lg font-bold text-green-600"><?= number_format($search_result['montant_total'], 2, ',', ' ') ?> FC</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <?php if($search_result['statut'] === 'ATTENTE'): ?>
                                <form action="<?= BASE_URL ?>/?action=process_payment" method="POST" class="flex-1">
                                    <input type="hidden" name="reservation_id" value="<?= $search_result['id'] ?>">
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        <i class="fa-solid fa-check mr-2"></i> Encaisser
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="flex-1 px-4 py-2 bg-gray-100 rounded-md text-center text-gray-600">
                                    Statut: <span class="font-bold"><?= htmlspecialchars($search_result['statut']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Liste des réservations en attente -->
            <h3 class="text-lg font-medium text-gray-900 mb-4">Réservations en attente de paiement</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activité</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Prévue</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($pending_reservations)): ?>
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune réservation en attente</td></tr>
                        <?php else: ?>
                            <?php foreach($pending_reservations as $r): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($r['client_nom']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($r['activite_nom']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($r['date_reservation'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?= number_format($r['montant_total'], 2, ',', ' ') ?> FC</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="<?= BASE_URL ?>/?action=process_payment" method="POST">
                                            <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                            <button type="submit" class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded-md border border-green-200 transition-colors">Encaisser</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
