<?php require 'views/layout/header.php';
$users_count = $users_count ?? 0;
$active_users = $active_users ?? 0;
$recent_logins = $recent_logins ?? [];
$suspicious_logins = $suspicious_logins ?? [];
$recent_activities = $recent_activities ?? [];
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Tableau de Bord Administrateur</h1>
            <p class="text-slate-400">Bienvenue, <?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?></p>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Utilisateurs Total -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-blue-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm mb-2">Utilisateurs Total</p>
                        <p class="text-3xl font-bold text-white"><?= $users_count ?? 0 ?></p>
                    </div>
                    <div class="text-blue-400">
                        <i class="fas fa-users text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Utilisateurs Actifs -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-green-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm mb-2">Utilisateurs Actifs</p>
                        <p class="text-3xl font-bold text-white"><?= $active_users ?? 0 ?></p>
                    </div>
                    <div class="text-green-400">
                        <i class="fas fa-check-circle text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Connexions Suspectes -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-red-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm mb-2">Connexions Suspectes</p>
                        <p class="text-3xl font-bold text-white"><?= count($suspicious_logins ?? []) ?></p>
                    </div>
                    <div class="text-red-400">
                        <i class="fas fa-exclamation-triangle text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Activités -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-purple-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm mb-2">Activités Récentes</p>
                        <p class="text-3xl font-bold text-white"><?= count($recent_activities ?? []) ?></p>
                    </div>
                    <div class="text-purple-400">
                        <i class="fas fa-chart-line text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Rapide -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <a href="<?= BASE_URL ?>/?action=admin_users" class="group">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-blue-500/50 transition-all cursor-pointer hover:bg-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Gestion des Utilisateurs</h3>
                        <i class="fas fa-arrow-right text-slate-400 group-hover:text-blue-400 transition-colors"></i>
                    </div>
                    <p class="text-slate-400 text-sm">Gérer les comptes, activer/désactiver</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>/?action=admin_login_history" class="group">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-green-500/50 transition-all cursor-pointer hover:bg-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Historique des Connexions</h3>
                        <i class="fas fa-arrow-right text-slate-400 group-hover:text-green-400 transition-colors"></i>
                    </div>
                    <p class="text-slate-400 text-sm">Consulter les logs de connexion</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>/?action=admin_audit_logs" class="group">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-purple-500/50 transition-all cursor-pointer hover:bg-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Journaux d'Audit</h3>
                        <i class="fas fa-arrow-right text-slate-400 group-hover:text-purple-400 transition-colors"></i>
                    </div>
                    <p class="text-slate-400 text-sm">Voir toutes les activités du système</p>
                </div>
            </a>

            <?php if ($_SESSION['user_role'] === 'SUPER_ADMIN'): ?>
            <a href="<?= BASE_URL ?>/?action=admin_roles" class="group">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-orange-500/50 transition-all cursor-pointer hover:bg-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Gestion des Rôles</h3>
                        <i class="fas fa-arrow-right text-slate-400 group-hover:text-orange-400 transition-colors"></i>
                    </div>
                    <p class="text-slate-400 text-sm">Gérer les permissions et les rôles</p>
                </div>
            </a>
            <?php endif; ?>
        </div>

        <!-- Connexions Suspectes Récentes -->
        <?php if (!empty($suspicious_logins)): ?>
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-shield-alt text-red-400"></i>
                    Alerte: Connexions Suspectes Détectées
                </h2>
                <a href="<?= BASE_URL ?>/?action=admin_login_history" class="text-blue-400 hover:text-blue-300 text-sm">Voir plus →</a>
            </div>

            <div class="space-y-4">
                <?php foreach ($suspicious_logins as $login): ?>
                <div class="bg-red-500/10 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white font-semibold"><?= htmlspecialchars($login['first_name'] ?? '') ?> <?= htmlspecialchars($login['last_name'] ?? '') ?></p>
                            <p class="text-slate-400 text-sm">IP: <?= htmlspecialchars($login['ip_address']) ?> | <?= htmlspecialchars($login['browser']) ?> - <?= htmlspecialchars($login['os']) ?></p>
                            <p class="text-slate-400 text-sm"><?= date('d/m/Y à H:i', strtotime($login['login_time'])) ?></p>
                        </div>
                        <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded text-sm">Suspecte</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Activités Récentes -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-history text-blue-400"></i>
                    Activités Récentes
                </h2>
                <a href="<?= BASE_URL ?>/?action=admin_audit_logs" class="text-blue-400 hover:text-blue-300 text-sm">Voir tous →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left py-3 px-4 text-slate-400 font-semibold">Utilisateur</th>
                            <th class="text-left py-3 px-4 text-slate-400 font-semibold">Action</th>
                            <th class="text-left py-3 px-4 text-slate-400 font-semibold">Entité</th>
                            <th class="text-left py-3 px-4 text-slate-400 font-semibold">Date/Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_activities as $activity): ?>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4 text-white"><?= htmlspecialchars($activity['user_name'] ?? 'Système') ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded text-xs">
                                    <?= htmlspecialchars($activity['action']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-400"><?= htmlspecialchars($activity['entity_type'] ?? '-') ?></td>
                            <td class="py-3 px-4 text-slate-400 text-xs"><?= date('d/m/Y H:i', strtotime($activity['timestamp'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
