<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <a href="<?= BASE_URL ?>/?action=dashboard" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">Tableau de Bord / KPIs</a>
        <?php if ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'DIRECTEUR'): ?>
            <a href="<?= BASE_URL ?>/?action=admin_activities" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">Gérer les Activités</a>
            <a href="<?= BASE_URL ?>/?action=reports" class="py-2 px-4 text-sm font-medium text-brand-600 border-b-2 border-brand-500">États en Sortie</a>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Rapports d'États en Sortie</h2>
            <div class="text-sm text-gray-500">Filtrez les réservations pour voir les clients venus en famille, les consommations, etc.</div>
        </div>

        <div class="p-6 border-b border-gray-200 bg-white">
            <form action="<?= BASE_URL ?>/?action=reports" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>" required class="block w-full rounded-md border-gray-300 p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>" required class="block w-full rounded-md border-gray-300 p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activité ciblée</label>
                    <select name="activite_id" class="block w-full rounded-md border-gray-300 p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Toutes les activités (Global)</option>
                        <?php foreach($activities as $act): ?>
                            <option value="<?= $act['id'] ?>" <?= ($activite_id == $act['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($act['nom']) ?> (<?= htmlspecialchars($act['type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 text-white p-2 rounded-md font-medium hover:bg-indigo-700 transition">Générer le rapport</button>
                </div>
            </form>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="p-6 bg-indigo-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-indigo-900">Résultats du filtre</h3>
                    <p class="text-sm text-indigo-700"><?= count($reservations) ?> réservation(s) trouvée(s).</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Chiffre d'affaires lié (Confirmé/Payé)</p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($total_amount, 0, ',', ' ') ?> FC</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activité Consommée</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun résultat pour cette période.</td></tr>
                    <?php else: ?>
                        <?php foreach($reservations as $r): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($r['date_reservation'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($r['client_nom']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="font-semibold text-indigo-600"><?= htmlspecialchars($r['activite_nom']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if($r['statut'] == 'PAYEE'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Payée</span>
                                <?php elseif($r['statut'] == 'CONFIRMEE'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Confirmée</span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"><?= htmlspecialchars($r['statut']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900"><?= number_format($r['montant_total'], 0, ',', ' ') ?> FC</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
