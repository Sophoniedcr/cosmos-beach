<?php require 'views/layout/header.php';
$roles = $roles ?? [];
$permissions = $permissions ?? [];
$role_permissions = $role_permissions ?? [];
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Gestion des Rôles et Permissions</h1>
            <p class="text-slate-400">Configurez les permissions pour chaque rôle</p>
        </div>

        <!-- Onglets des Rôles -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden mb-8">
            <div class="flex flex-wrap border-b border-white/10">
                <?php foreach ($roles as $role): ?>
                <button class="role-tab px-6 py-4 text-white border-b-2 border-transparent hover:border-blue-500/50 transition-all cursor-pointer" data-role="<?= htmlspecialchars($role) ?>">
                    <i class="fas fa-shield-alt mr-2"></i><?= htmlspecialchars($role) ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Contenu des Onglets -->
            <?php foreach ($roles as $role): ?>
            <div class="role-content hidden p-6" id="role-<?= htmlspecialchars($role) ?>">
                <form method="POST" action="<?= BASE_URL ?>/?action=update_role_permissions">
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                    <!-- Catégories de Permissions -->
                    <?php 
                    $permissions_by_category = [];
                    foreach ($permissions as $perm) {
                        $cat = $perm['category'] ?? 'general';
                        if (!isset($permissions_by_category[$cat])) {
                            $permissions_by_category[$cat] = [];
                        }
                        $permissions_by_category[$cat][] = $perm;
                    }

                    $current_perms = array_map(function($p) { return $p['id']; }, $role_permissions[$role] ?? []);
                    ?>

                    <?php foreach ($permissions_by_category as $category => $perms): ?>
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-folder text-blue-400"></i>
                            <?= ucfirst(htmlspecialchars($category)) ?>
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($perms as $perm): ?>
                            <label class="flex items-center gap-3 p-4 bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 cursor-pointer transition-all">
                                <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" 
                                       <?= in_array($perm['id'], $current_perms) ? 'checked' : '' ?>
                                       class="w-4 h-4 rounded border-white/20 bg-white/10 accent-blue-500">
                                <div>
                                    <p class="text-white font-semibold text-sm"><?= htmlspecialchars($perm['name']) ?></p>
                                    <p class="text-slate-400 text-xs"><?= htmlspecialchars($perm['description'] ?? '') ?></p>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="flex gap-4 pt-6 border-t border-white/10">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Enregistrer les Permissions
                        </button>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Résumé des Permissions par Rôle -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-list text-purple-400"></i>
                Résumé des Permissions par Rôle
            </h2>

            <div class="space-y-4">
                <?php foreach ($roles as $role): ?>
                <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-user-shield text-blue-400"></i>
                            <?= htmlspecialchars($role) ?>
                        </h3>
                        <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm font-semibold">
                            <?= count($role_permissions[$role] ?? []) ?> permissions
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($role_permissions[$role] ?? [] as $perm): ?>
                        <span class="px-3 py-1 bg-slate-700/50 text-slate-300 rounded-full text-xs">
                            <?= htmlspecialchars($perm['name']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.role-tab');
    const contents = document.querySelectorAll('.role-content');

    // Afficher le premier onglet par défaut
    if (tabs.length > 0) {
        tabs[0].classList.add('border-blue-500/50');
        const firstRole = tabs[0].getAttribute('data-role');
        document.getElementById('role-' + firstRole).classList.remove('hidden');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const role = this.getAttribute('data-role');

            // Masquer tous les contenus
            contents.forEach(content => {
                content.classList.add('hidden');
            });

            // Retirer la classe active de tous les onglets
            tabs.forEach(t => {
                t.classList.remove('border-blue-500/50');
                t.classList.add('border-transparent');
            });

            // Afficher le contenu sélectionné et mettre à jour l'onglet
            document.getElementById('role-' + role).classList.remove('hidden');
            this.classList.remove('border-transparent');
            this.classList.add('border-blue-500/50');
        });
    });
});
</script>

<?php require 'views/layout/footer.php'; ?>
