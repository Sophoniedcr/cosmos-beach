<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Sous-menu (onglets) -->
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <a href="<?= BASE_URL ?>/?action=dashboard" class="py-2 px-4 text-sm font-medium text-brand-600 border-b-2 border-brand-500">Tableau de Bord / KPIs</a>
        <a href="<?= BASE_URL ?>/?action=admin_activities" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">Gérer les Activités</a>
        <a href="<?= BASE_URL ?>/?action=reports" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">États en Sortie</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Dashboard Directeur -->
        <div class="bg-gradient-to-r from-purple-700 to-indigo-800 px-6 py-8 sm:p-10">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Tableau de Bord Direction</h1>
                    <p class="mt-2 text-purple-200">
                        Aperçu global des performances de Cosmos Beach
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200">
                        <i class="fa-solid fa-chart-line mr-2"></i> Mode Supervision
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Bouton Historique -->
            <div class="mb-6 flex flex-wrap gap-3 justify-end">
              <a href="<?= BASE_URL ?>/?action=mon_historique"
                 class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white font-semibold rounded-xl shadow transition text-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Historique Équipe
              </a>
              <a href="<?= BASE_URL ?>/?action=admin_manage_permissions"
                 class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-purple-700 border border-purple-300 font-semibold rounded-xl shadow transition text-sm">
                <i class="fa-solid fa-key"></i> Droits Employés
              </a>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-6">Indicateurs Clés de Performance (KPIs)</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Chiffre d'affaires Global -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fa-solid fa-vault text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 font-medium">Revenu Global</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($totalRevenue, 2, ',', ' ') ?> FC</p>
                        </div>
                    </div>
                </div>

                <!-- Chiffre d'affaires du Jour -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fa-solid fa-hand-holding-dollar text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 font-medium">Revenu du Jour</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($todayRevenue, 2, ',', ' ') ?> FC</p>
                        </div>
                    </div>
                </div>

                <!-- Réservations Totales -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <i class="fa-solid fa-users text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 font-medium">Total Réservations</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($totalReservations, 0, ',', ' ') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-8 rounded-xl border border-gray-200 text-center">
                 <i class="fa-solid fa-chart-pie text-gray-300 text-6xl mb-4"></i>
                 <h3 class="text-lg font-medium text-gray-900">Rapports Détaillés</h3>
                 <p class="text-gray-500 mt-2">Utilisez le nouveau module d'États en Sortie pour filtrer les réservations et générer vos bilans.</p>
                 <a href="<?= BASE_URL ?>/?action=reports" class="mt-4 inline-block px-6 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 transition">Aller aux États en Sortie</a>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
