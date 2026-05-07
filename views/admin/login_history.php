<?php require 'views/layout/header.php';
$user_filter = $user_filter ?? '';
$page = $page ?? 1;
$total_pages = $total_pages ?? 1;
$logins = $logins ?? [];
$suspicious_logins = $suspicious_logins ?? [];
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Historique des Connexions</h1>
            <p class="text-slate-400">Consultez toutes les tentatives de connexion et identifiez les activités suspectes</p>
        </div>

        <!-- Alerte Connexions Suspectes -->
        <?php if (!empty($suspicious_logins)): ?>
        <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-4 mb-8">
            <div class="flex items-start gap-4">
                <i class="fas fa-exclamation-triangle text-red-400 text-2xl mt-1"></i>
                <div>
                    <h3 class="text-red-300 font-semibold mb-1">⚠️ Alerte: <?= count($suspicious_logins) ?> connexion(s) suspecte(s)</h3>
                    <p class="text-red-200 text-sm">Des connexions ont été détectées depuis une adresse IP différente des habituelles.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filtres -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="action" value="admin_login_history">
                
                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Utilisateur</label>
                    <input type="number" name="user_id" value="<?= htmlspecialchars($user_filter) ?>" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-blue-500/50"
                           placeholder="ID utilisateur...">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Connexions Suspectes en Détail -->
        <?php if (!empty($suspicious_logins)): ?>
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 mb-8">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-shield-alt text-red-400"></i>
                Connexions Suspectes Détectées
            </h2>

            <div class="space-y-4">
                <?php foreach ($suspicious_logins as $login): ?>
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Utilisateur</p>
                            <p class="text-white font-semibold"><?= htmlspecialchars($login['first_name'] ?? '') ?> <?= htmlspecialchars($login['last_name'] ?? '') ?></p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Email</p>
                            <p class="text-white"><?= htmlspecialchars($login['email'] ?? '') ?></p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Adresse IP</p>
                            <p class="text-white font-mono"><?= htmlspecialchars($login['ip_address']) ?></p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Date/Heure</p>
                            <p class="text-white"><?= date('d/m/Y H:i:s', strtotime($login['login_time'])) ?></p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Navigateur / OS</p>
                            <p class="text-white text-sm"><?= htmlspecialchars($login['browser']) ?> / <?= htmlspecialchars($login['os']) ?></p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs mb-1">Type d'Appareil</p>
                            <p class="text-white"><?= htmlspecialchars($login['device_type']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tableau Connexions -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Utilisateur</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Email</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Adresse IP</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Navigateur</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Statut</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Date/Heure Connexion</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Date/Heure Déconnexion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logins)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                Aucune connexion trouvée
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($logins as $login): ?>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="py-4 px-6 text-white font-semibold">
                                    <?= htmlspecialchars($login['first_name'] ?? '') ?> <?= htmlspecialchars($login['last_name'] ?? '') ?>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    <?= htmlspecialchars($login['email'] ?? '') ?>
                                </td>
                                <td class="py-4 px-6 text-slate-300 font-mono text-xs">
                                    <?= htmlspecialchars($login['ip_address']) ?>
                                </td>
                                <td class="py-4 px-6 text-slate-300 text-xs">
                                    <?= htmlspecialchars($login['browser']) ?> / <?= htmlspecialchars($login['os']) ?>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if ($login['status'] === 'success'): ?>
                                        <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-xs font-semibold flex items-center gap-1 w-fit">
                                            <i class="fas fa-check-circle"></i> Réussi
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-xs font-semibold flex items-center gap-1 w-fit">
                                            <i class="fas fa-times-circle"></i> Échoué
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($login['is_suspicious']): ?>
                                        <span class="px-2 py-1 bg-red-500/30 text-red-300 rounded text-xs font-semibold ml-2">
                                            <i class="fas fa-shield-alt"></i> Suspecte
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    <?= date('d/m/Y H:i:s', strtotime($login['login_time'])) ?>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    <?= $login['logout_time'] ? date('d/m/Y H:i:s', strtotime($login['logout_time'])) : 'En cours...' ?>
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
                    <a href="<?= BASE_URL ?>/?action=admin_login_history&page=<?= $page - 1 ?>&user_id=<?= urlencode($user_filter) ?>" 
                       class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <span class="text-slate-400 text-sm">Page <?= $page ?> / <?= $total_pages ?></span>

                <?php if ($page < $total_pages): ?>
                    <a href="<?= BASE_URL ?>/?action=admin_login_history&page=<?= $page + 1 ?>&user_id=<?= urlencode($user_filter) ?>" 
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
