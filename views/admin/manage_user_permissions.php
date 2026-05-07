<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-8 sm:p-10">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Gestion des Permissions Utilisateurs</h1>
                    <p class="mt-2 text-indigo-100">
                        <?php if($_SESSION['user_role'] === 'SUPER_ADMIN'): ?>
                            Gérez les droits d'accès de tous les utilisateurs
                        <?php else: ?>
                            Gérez les droits d'accès de vos employés
                        <?php endif; ?>
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                        <i class="fa-solid fa-users mr-2"></i> Total: <strong class="ml-2"><?= count($users) ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Messages -->
            <?php if(!empty($_SESSION['flash_success'])): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                    <i class="fa-solid fa-check-circle text-green-600 mt-1"></i>
                    <span class="text-green-700"><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if(!empty($_SESSION['flash_error'])): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-600 mt-1"></i>
                    <span class="text-red-700"><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <!-- Liste des utilisateurs -->
            <?php if(empty($users)): ?>
                <div class="text-center py-12">
                    <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 font-medium text-lg">Aucun utilisateur trouvé</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-6">
                    <?php foreach($users as $user): ?>
                        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                            <!-- Header utilisateur -->
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        <?= substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            <i class="fa-solid fa-envelope mr-1"></i><?= htmlspecialchars($user['email']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fa-solid fa-shield mr-1"></i><?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Permissions -->
                            <form method="POST" action="<?= BASE_URL ?>/?action=update_user_permissions" class="space-y-4">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        <i class="fa-solid fa-key mr-2"></i>Permissions
                                    </label>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <?php foreach($all_permissions as $perm): 
                                            $is_checked = false;
                                            if(isset($user_permissions[$user['id']])) {
                                                foreach($user_permissions[$user['id']] as $up) {
                                                    if($up['id'] == $perm['id']) {
                                                        $is_checked = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                                <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" 
                                                       <?= $is_checked ? 'checked' : '' ?>
                                                       class="w-5 h-5 rounded text-indigo-600 focus:ring-2 focus:ring-indigo-500 cursor-pointer mt-1">
                                                <div>
                                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($perm['name']) ?></p>
                                                    <p class="text-xs text-gray-600">
                                                        <i class="fa-solid fa-tag mr-1"></i><?= htmlspecialchars($perm['category']) ?>
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <?= htmlspecialchars($perm['description']) ?>
                                                    </p>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Boutons -->
                                <div class="flex gap-3 pt-4 border-t border-gray-200">
                                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                        <i class="fa-solid fa-check mr-2"></i>Enregistrer
                                    </button>
                                    <button type="reset" class="inline-flex items-center px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors">
                                        <i class="fa-solid fa-times mr-2"></i>Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
