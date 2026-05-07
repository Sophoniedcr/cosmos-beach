<?php require 'views/layout/header.php';
$search = $search ?? '';
$role_filter = $role_filter ?? '';
$status_filter = $status_filter ?? '';
$users = $users ?? [];
$page = $page ?? 1;
$total_pages = $total_pages ?? 1;
$total_users = $total_users ?? 0;
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Gestion des Utilisateurs</h1>
            <p class="text-slate-400">Gérer les comptes, activer/désactiver les utilisateurs</p>
        </div>

        <!-- Filtres et Recherche -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="action" value="admin_users">
                
                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Rechercher</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-blue-500/50"
                           placeholder="Nom, email...">
                </div>

                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Rôle</label>
                    <select name="role" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-500/50">
                        <option value="">Tous</option>
                        <option value="SUPER_ADMIN" <?= $role_filter === 'SUPER_ADMIN' ? 'selected' : '' ?>>Super Admin</option>
                        <option value="DIRECTEUR" <?= $role_filter === 'DIRECTEUR' ? 'selected' : '' ?>>Directeur</option>
                        <option value="AGENT" <?= $role_filter === 'AGENT' ? 'selected' : '' ?>>Agent</option>
                        <option value="CAISSIER" <?= $role_filter === 'CAISSIER' ? 'selected' : '' ?>>Caissier</option>
                        <option value="VISITEUR" <?= $role_filter === 'VISITEUR' ? 'selected' : '' ?>>Visiteur</option>
                    </select>
                </div>

                <div>
                    <label class="block text-white text-sm font-semibold mb-2">Statut</label>
                    <select name="status" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-500/50">
                        <option value="">Tous</option>
                        <option value="1" <?= $status_filter === '1' ? 'selected' : '' ?>>Actif</option>
                        <option value="0" <?= $status_filter === '0' ? 'selected' : '' ?>>Désactivé</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau des utilisateurs -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Nom</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Email</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Rôle</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Statut</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Création</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Dernière Connexion</th>
                            <th class="text-left py-4 px-6 text-slate-400 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="py-4 px-6 text-white">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                            <?= strtoupper(substr($user['nom'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <?= htmlspecialchars($user['nom'] ?? '') ?>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-300"><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs font-semibold">
                                        <?= htmlspecialchars($user['role'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if ($user['is_active']): ?>
                                        <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-xs font-semibold flex items-center gap-1 w-fit">
                                            <i class="fas fa-check-circle"></i> Actif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-xs font-semibold flex items-center gap-1 w-fit">
                                            <i class="fas fa-times-circle"></i> Désactivé
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais' ?>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex gap-2">
                                        <?php if ($user['is_active']): ?>
                                            <form method="POST" action="<?= BASE_URL ?>/?action=toggle_user_status" class="inline">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="action" value="deactivate">
                                                <input type="hidden" name="reason" value="">
                                                <button type="submit" class="text-red-400 hover:text-red-300 transition-colors" title="Désactiver">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="<?= BASE_URL ?>/?action=toggle_user_status" class="inline">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="action" value="activate">
                                                <button type="submit" class="text-green-400 hover:text-green-300 transition-colors" title="Activer">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
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
                    <a href="<?= BASE_URL ?>/?action=admin_users&page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>" 
                       class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <span class="text-slate-400 text-sm">Page <?= $page ?> / <?= $total_pages ?></span>

                <?php if ($page < $total_pages): ?>
                    <a href="<?= BASE_URL ?>/?action=admin_users&page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>" 
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
