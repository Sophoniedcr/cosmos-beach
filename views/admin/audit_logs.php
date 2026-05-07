<?php require 'views/layout/header.php';
$action_filter = $action_filter ?? '';
$user_filter = $user_filter ?? '';
$start_date = $start_date ?? '';
$end_date = $end_date ?? '';
$page = $page ?? 1;
$total_pages = $total_pages ?? 1;
$logs = $logs ?? [];
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Journaux d'Audit</h1>
            <p class="text-slate-400">Consultez toutes les activités et actions du système</p>
        </div>

        <!-- Filtres -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="hidden" name="action" value="admin_audit_logs">
                
                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Action</label>
                    <input type="text" name="action" value="<?= htmlspecialchars($action_filter) ?>" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-blue-500/50"
                           placeholder="Type d'action...">
                </div>

                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Utilisateur</label>
                    <input type="number" name="user_id" value="<?= htmlspecialchars($user_filter) ?>" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-blue-500/50"
                           placeholder="ID utilisateur...">
                </div>

                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Du</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-500/50">
                </div>

                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Au</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-500/50">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau Journaux -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Utilisateur</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Action</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Type d'Entité</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Description</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Adresse IP</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Statut</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Date/Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                Aucun journal trouvé
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="py-4 px-6 text-white font-semibold">
                                    <?= htmlspecialchars($log['user_name'] ?? 'Système') ?>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs font-semibold">
                                        <?= htmlspecialchars($log['action']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    <?= htmlspecialchars($log['entity_type'] ?? '-') ?>
                                </td>
                                <td class="py-4 px-6 text-slate-300 text-xs">
                                    <?= htmlspecialchars(substr($log['description'] ?? '-', 0, 50)) ?>...
                                </td>
                                <td class="py-4 px-6 text-slate-300 font-mono text-xs">
                                    <?= htmlspecialchars($log['ip_address'] ?? '-') ?>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if ($log['status'] === 'success'): ?>
                                        <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check-circle mr-1"></i>Succès
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-xs font-semibold">
                                            <i class="fas fa-times-circle mr-1"></i>Échoué
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    <?= date('d/m/Y H:i:s', strtotime($log['timestamp'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center items-center gap-2 py-6 px-6 border-t border-white/10">
                <?php if ($page > 1): ?>
                    <a href="<?= BASE_URL ?>/?action=admin_audit_logs&page=<?= $page - 1 ?>" 
                       class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <span class="text-slate-400 text-sm">Page <?= $page ?> / <?= $total_pages ?></span>

                <?php if ($page < $total_pages): ?>
                    <a href="<?= BASE_URL ?>/?action=admin_audit_logs&page=<?= $page + 1 ?>" 
                       class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
