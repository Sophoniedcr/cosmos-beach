<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Dashboard Reception/Agent -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-700 px-6 py-8 sm:p-10">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Module Réception & Réservation</h1>
                    <p class="mt-2 text-blue-100">
                        Gestionnaire : <span class="font-semibold"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        <i class="fa-solid fa-bell-concierge mr-2"></i> Mode Front-Desk
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Bouton Historique -->
            <div class="mb-6 flex justify-end">
              <a href="<?= BASE_URL ?>/?action=mon_historique"
                 class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-xl shadow transition text-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Voir Mon Historique
              </a>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-6">Gestion Journalière</h2>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center text-blue-800 mb-8">
                <i class="fa-solid fa-users text-4xl mb-4 text-blue-300"></i>
                <h3 class="text-lg font-medium">Panneau d'enregistrement Rapide</h3>
                <p class="text-sm mt-2 text-blue-600">Enregistrer de nouveaux clients arrivant sans réservation (Walk-ins).</p>
                <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-full transition-colors shadow-sm">
                    + Nouvel Arrivant
                </button>
            </div>

            <!-- Simulation du planning -->
            <h3 class="text-lg font-medium text-gray-900 mb-4">Planning des Activités du Jour</h3>
            <div class="border rounded-lg overflow-hidden bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaire</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activité</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacité</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Aujourd'hui</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Piscine VIP</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">25 / 50 places</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disponible</span></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Aujourd'hui</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Restaurant Gastronomique</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">95 / 100 places</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Presque Complet</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
