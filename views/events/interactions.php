<?php require 'views/layout/header.php'; ?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <a href="<?= BASE_URL ?>/?action=marketing_dashboard"
       class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-800 text-sm font-medium mb-6 group">
        <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Retour au dashboard
    </a>

    <!-- Résumé de l'événement -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-4">
            <?php if ($event['image_url']): ?>
                <img src="<?= htmlspecialchars($event['image_url']) ?>" class="w-16 h-16 rounded-xl object-cover">
            <?php else: ?>
                <div class="w-16 h-16 rounded-xl cb-gradient flex items-center justify-center">
                    <i class="fa-solid fa-star text-white text-2xl"></i>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($event['titre']) ?></h1>
                <p class="text-sm text-gray-500">
                    <?= date('d/m/Y', strtotime($event['date_debut'])) ?> → <?= date('d/m/Y', strtotime($event['date_fin'])) ?>
                    <?php if ($event['lieu']): ?> · <?= htmlspecialchars($event['lieu']) ?><?php endif; ?>
                </p>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-4 text-center">
            <div class="bg-green-50 rounded-xl p-3">
                <p class="text-xl font-bold text-green-700"><?= count(array_filter($tickets, fn($t) => $t['statut'] === 'CONFIRME')) ?></p>
                <p class="text-xs text-green-600 font-medium">Confirmés</p>
            </div>
            <div class="bg-yellow-50 rounded-xl p-3">
                <p class="text-xl font-bold text-yellow-700"><?= count(array_filter($tickets, fn($t) => $t['statut'] === 'EN_ATTENTE')) ?></p>
                <p class="text-xs text-yellow-600 font-medium">En attente</p>
            </div>
            <div class="bg-brand-50 rounded-xl p-3">
                <p class="text-xl font-bold text-brand-700"><?= number_format(array_sum(array_column(array_filter($tickets, fn($t) => $t['statut'] === 'CONFIRME'), 'montant_total')), 0, ',', ' ') ?> FC</p>
                <p class="text-xs text-brand-600 font-medium">Revenus</p>
            </div>
        </div>
    </div>

    <!-- Tableau des tickets -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-users text-brand-500"></i>
                Interactions — <?= count($tickets) ?> ticket(s) vendus
            </h2>
        </div>
        <?php if (empty($tickets)): ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fa-solid fa-ticket text-4xl text-gray-200 mb-3"></i>
                <p>Aucun ticket réservé pour cet événement.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3 text-left">N° Ticket</th>
                            <th class="px-5 py-3 text-left">Client</th>
                            <th class="px-5 py-3 text-left">Email</th>
                            <th class="px-5 py-3 text-center">Places</th>
                            <th class="px-5 py-3 text-right">Montant</th>
                            <th class="px-5 py-3 text-center">Statut</th>
                            <th class="px-5 py-3 text-center">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($tickets as $t): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <span class="font-mono text-xs font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-lg">
                                    <?= htmlspecialchars($t['numero_ticket']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-900">
                                <?= htmlspecialchars($t['client_prenom'] . ' ' . $t['client_nom']) ?>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs"><?= htmlspecialchars($t['client_email']) ?></td>
                            <td class="px-5 py-3 text-center font-semibold"><?= $t['nombre_places'] ?></td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900">
                                <?= number_format((float)$t['montant_total'], 0, ',', ' ') ?> FC
                            </td>
                            <td class="px-5 py-3 text-center">
                                <?php
                                    $cls = match($t['statut']) {
                                        'CONFIRME'  => 'bg-green-100 text-green-800',
                                        'ANNULE'    => 'bg-red-100 text-red-800',
                                        default     => 'bg-yellow-100 text-yellow-800',
                                    };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $cls ?>">
                                    <?= $t['statut'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center text-xs text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($t['date_achat'])) ?>
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
