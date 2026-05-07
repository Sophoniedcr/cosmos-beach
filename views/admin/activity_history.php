<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Navigation breadcrumb -->
    <div class="mb-6 text-sm">
        <a href="<?= BASE_URL ?>/?action=dashboard" class="text-indigo-600 hover:text-indigo-700">Tableau de Bord</a>
        <span class="mx-2 text-gray-400">›</span>
        <a href="<?= BASE_URL ?>/?action=admin_dashboard" class="text-indigo-600 hover:text-indigo-700">Admin</a>
        <span class="mx-2 text-gray-400">›</span>
        <span class="text-gray-600">Historique d'Activité</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-teal-700 px-6 py-8 sm:p-10">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Historique des Activités</h1>
                    <p class="mt-2 text-green-100">
                        Suivi complet des modifications et créations d'activités
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                        <i class="fa-solid fa-history mr-2"></i> Total: <strong class="ml-2"><?= $total ?? 0 ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Filtres -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fa-solid fa-filter mr-2"></i>Filtres
                </h3>
                <form method="GET" action="<?= BASE_URL ?>/?action=activity_history" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="activity_id" class="block text-sm font-medium text-gray-700 mb-1">Activité ID</label>
                        <input type="number" id="activity_id" name="activity_id" placeholder="Ex: 1"
                               value="<?= isset($_GET['activity_id']) ? htmlspecialchars($_GET['activity_id']) : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                        <select id="action" name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                            <option value="">-- Toutes les actions --</option>
                            <option value="CREATE" <?= isset($_GET['action']) && $_GET['action'] === 'CREATE' ? 'selected' : '' ?>>Création</option>
                            <option value="UPDATE" <?= isset($_GET['action']) && $_GET['action'] === 'UPDATE' ? 'selected' : '' ?>>Modification</option>
                            <option value="DELETE" <?= isset($_GET['action']) && $_GET['action'] === 'DELETE' ? 'selected' : '' ?>>Suppression</option>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                        <input type="date" id="start_date" name="start_date"
                               value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>

                    <div class="flex items-end gap-3">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fa-solid fa-search mr-2"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Timeline -->
            <div class="space-y-4">
                <?php if(empty($history)): ?>
                    <div class="text-center py-12">
                        <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-medium">Aucun historique trouvé</p>
                    </div>
                <?php else: ?>
                    <?php foreach($history as $item): 
                        $action_icon = 'fa-edit';
                        $action_color = 'bg-blue-100 text-blue-800';
                        $action_text = 'Modification';

                        if ($item['action'] === 'CREATE') {
                            $action_icon = 'fa-plus-circle';
                            $action_color = 'bg-green-100 text-green-800';
                            $action_text = 'Création';
                        } elseif ($item['action'] === 'DELETE') {
                            $action_icon = 'fa-trash';
                            $action_color = 'bg-red-100 text-red-800';
                            $action_text = 'Suppression';
                        }
                    ?>
                        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                            <div class="flex gap-4">
                                <!-- Icône action -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center <?= $action_color ?>">
                                        <i class="fa-solid <?= $action_icon ?>"></i>
                                    </div>
                                </div>

                                <!-- Contenu -->
                                <div class="flex-grow">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="font-bold text-gray-900">
                                                Activité #<?= $item['activity_id'] ?>
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fa-solid fa-tag mr-2"></i>
                                                <span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs font-medium">
                                                    <?= $action_text ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">
                                                <i class="fa-solid fa-calendar-days mr-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Utilisateur -->
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <p class="text-sm text-gray-700">
                                            <i class="fa-solid fa-user mr-2"></i>
                                            <strong><?= htmlspecialchars(($item['prenom'] ?? 'N/A') . ' ' . ($item['nom'] ?? 'N/A')) ?></strong>
                                            <span class="text-gray-500">(<?= htmlspecialchars($item['email'] ?? 'N/A') ?>)</span>
                                        </p>
                                    </div>

                                    <!-- Champs modifiés -->
                                    <?php if($item['action'] === 'UPDATE' && $item['changed_fields']): 
                                        $changed = json_decode($item['changed_fields'], true);
                                    ?>
                                        <div class="mt-3">
                                            <p class="text-sm font-medium text-gray-700 mb-2">
                                                <i class="fa-solid fa-pen mr-1"></i>Champs modifiés:
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach($changed as $field): ?>
                                                    <span class="inline-block px-2 py-1 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700 font-medium">
                                                        <?= htmlspecialchars($field) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Détails complets (expandable) -->
                                    <?php if($item['old_values'] || $item['new_values']): ?>
                                        <details class="mt-3">
                                            <summary class="cursor-pointer text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                                <i class="fa-solid fa-info-circle mr-1"></i>Voir les détails
                                            </summary>
                                            <div class="mt-3 p-3 bg-gray-100 rounded-lg text-xs font-mono overflow-x-auto space-y-2">
                                                <?php if($item['old_values']): ?>
                                                    <div>
                                                        <p class="font-bold text-gray-700 mb-1">Avant:</p>
                                                        <pre class="text-gray-600"><?= htmlspecialchars($item['old_values']) ?></pre>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if($item['new_values']): ?>
                                                    <div>
                                                        <p class="font-bold text-gray-700 mb-1">Après:</p>
                                                        <pre class="text-gray-600"><?= htmlspecialchars($item['new_values']) ?></pre>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </details>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
