<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Header -->
    <div class="cb-gradient rounded-2xl px-6 py-8 sm:p-10 mb-8 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <i class="fa-solid fa-bullhorn"></i> Service Marketing
                </h1>
                <p class="text-blue-100 mt-1 text-sm">
                    Bienvenue, <strong><?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?></strong> — Gérez vos événements et promotions
                </p>
            </div>
            <a href="<?= BASE_URL ?>/?action=marketing_create"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-brand-700 font-bold rounded-xl shadow hover:bg-blue-50 transition text-sm">
                <i class="fa-solid fa-plus"></i> Nouvel Événement
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4">
            <div class="bg-blue-100 p-3 rounded-lg text-brand-600"><i class="fa-solid fa-calendar-star text-xl"></i></div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Événements</p>
                <p class="text-2xl font-bold text-gray-900"><?= count($events) ?></p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4">
            <div class="bg-green-100 p-3 rounded-lg text-green-600"><i class="fa-solid fa-ticket text-xl"></i></div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Tickets vendus</p>
                <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['confirmes'] ?? 0) ?></p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4">
            <div class="bg-indigo-100 p-3 rounded-lg text-indigo-600"><i class="fa-solid fa-coins text-xl"></i></div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Revenus</p>
                <p class="text-lg font-bold text-gray-900"><?= number_format((float)($stats['revenu_total'] ?? 0), 0, ',', ' ') ?> FC</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4">
            <div class="bg-red-100 p-3 rounded-lg text-red-500"><i class="fa-solid fa-ban text-xl"></i></div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Annulés</p>
                <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['annules'] ?? 0) ?></p>
            </div>
        </div>
    </div>

    <!-- Tableau des événements -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-list text-brand-500"></i> Mes Événements
            </h2>
        </div>

        <?php if (empty($events)): ?>
            <div class="p-16 text-center text-gray-500">
                <i class="fa-solid fa-calendar-xmark text-5xl text-gray-200 mb-4"></i>
                <p class="font-medium text-gray-700">Aucun événement publié pour l'instant.</p>
                <a href="<?= BASE_URL ?>/?action=marketing_create"
                   class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 cb-btn-primary rounded-xl text-sm font-semibold">
                    <i class="fa-solid fa-plus"></i> Créer votre premier événement
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-6 py-3 text-left">Événement</th>
                            <th class="px-6 py-3 text-left">Dates</th>
                            <th class="px-6 py-3 text-center">Prix ticket</th>
                            <th class="px-6 py-3 text-center">Tickets vendus</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($events as $e): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if ($e['image_url']): ?>
                                        <img src="<?= htmlspecialchars($e['image_url']) ?>" class="w-10 h-10 rounded-lg object-cover">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-lg cb-gradient flex items-center justify-center text-white text-sm">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($e['titre']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($e['lieu'] ?? 'Lieu non défini') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex flex-col text-xs">
                                    <span><i class="fa-solid fa-play mr-1 text-green-500"></i><?= date('d/m/Y', strtotime($e['date_debut'])) ?></span>
                                    <span><i class="fa-solid fa-stop mr-1 text-red-400"></i><?= date('d/m/Y', strtotime($e['date_fin'])) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-brand-700">
                                <?= number_format((float)$e['prix_ticket'], 0, ',', ' ') ?> FC
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fa-solid fa-ticket text-xs"></i>
                                    <?= (int)($e['tickets_vendus'] ?? 0) ?> / <?= (int)$e['capacite_max'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($e['is_active']): ?>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Actif</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= BASE_URL ?>/?action=marketing_interactions&id=<?= $e['id'] ?>"
                                       title="Voir les interactions"
                                       class="p-2 bg-blue-50 text-brand-600 hover:bg-blue-100 rounded-lg transition">
                                        <i class="fa-solid fa-users text-xs"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/?action=marketing_edit&id=<?= $e['id'] ?>"
                                       title="Modifier"
                                       class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/?action=marketing_toggle&id=<?= $e['id'] ?>"
                                       title="<?= $e['is_active'] ? 'Désactiver' : 'Activer' ?>"
                                       onclick="return confirm('<?= $e['is_active'] ? 'Désactiver cet événement ?' : 'Activer cet événement ?' ?>')"
                                       class="p-2 bg-<?= $e['is_active'] ? 'red' : 'green' ?>-50 text-<?= $e['is_active'] ? 'red' : 'green' ?>-600 hover:bg-<?= $e['is_active'] ? 'red' : 'green' ?>-100 rounded-lg transition">
                                        <i class="fa-solid fa-<?= $e['is_active'] ? 'eye-slash' : 'eye' ?> text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
