<?php require 'views/layout/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Sous-menu (onglets) -->
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <a href="<?= BASE_URL ?>/?action=dashboard" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">Tableau de Bord / KPIs</a>
        <a href="<?= BASE_URL ?>/?action=admin_activities" class="py-2 px-4 text-sm font-medium text-brand-600 border-b-2 border-brand-500">Gérer les Activités</a>
        <a href="<?= BASE_URL ?>/?action=reports" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">États en Sortie</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Catalogue des Activités</h2>
            <button onclick="document.getElementById('addForm').classList.toggle('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fa-solid fa-plus mr-1"></i> Nouvelle Activité
            </button>
        </div>

        <?php if(isset($_SESSION['flash_success'])): ?>
            <div class="bg-green-50 p-4 text-green-700 font-medium">
                <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout caché par défaut -->
        <div id="addForm" class="hidden p-6 border-b border-gray-200 bg-indigo-50">
            <form id="activityForm" action="<?= BASE_URL ?>/?action=admin_activities" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" id="action_type" name="action_type" value="add">
                <input type="hidden" id="update_id" name="update_id" value="">
                <input type="hidden" id="existing_image" name="existing_image" value="">
                
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="nom" name="nom" required class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm border focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Type d'activité</label>
                    <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm border focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="piscine_vip">Piscine VIP</option>
                        <option value="piscine_ordinaire">Piscine Ordinaire</option>
                        <option value="chambre">Chambre/Hébergement</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="zoo">Zoo/Visite</option>
                        <option value="jeux">Aires de jeux</option>
                    </select>
                </div>
                <div class="col-span-2 text-sm font-medium text-gray-700">
                    <label class="block">Description courte</label>
                    <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm border focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="col-span-2 md:col-span-1 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prix (FC)</label>
                        <input type="number" step="0.01" id="prix" name="prix" required class="mt-1 block w-full rounded-md border-gray-300 p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capa. Max</label>
                        <input type="number" id="capacite_max" name="capacite_max" required class="mt-1 block w-full rounded-md border-gray-300 p-2 border shadow-sm">
                    </div>
                </div>
                <div class="col-span-2 md:col-span-1 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Durée estimée</label>
                        <input type="text" id="duree" name="duree" placeholder="ex: journée, nuit, séjour" required class="mt-1 block w-full rounded-md border-gray-300 p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image de l'activité</label>
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 p-2 border shadow-sm bg-white">
                        <p id="current_image_text" class="text-xs text-gray-500 mt-1 hidden">Image actuelle conservée si vous n'en choisissez pas une nouvelle.</p>
                    </div>
                </div>
                <div class="col-span-2 mt-2 border-t pt-4 flex gap-2">
                    <button type="submit" id="submitBtn" class="w-full md:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700 shadow-sm transition">Ajouter l'activité</button>
                    <button type="button" onclick="cancelEdit()" class="w-full md:w-auto px-6 py-2 bg-gray-500 text-white rounded-md font-medium hover:bg-gray-600 shadow-sm transition hidden" id="cancelBtn">Annuler</button>
                </div>
            </form>
        </div>

        <!-- Table des activités -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacité</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($activities as $a): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($a['nom']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars(str_replace('_', ' ', $a['type'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600"><?= number_format($a['prix'], 0, ',', ' ') ?> FC</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($a['capacite_max']) ?> places</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="editActivity(<?= $a['id'] ?>, '<?= htmlspecialchars(addslashes($a['nom'])) ?>', '<?= htmlspecialchars(addslashes($a['type'])) ?>', '<?= htmlspecialchars(addslashes($a['description'])) ?>', <?= $a['prix'] ?>, <?= $a['capacite_max'] ?>, '<?= htmlspecialchars(addslashes($a['duree'])) ?>', '<?= htmlspecialchars(addslashes($a['image_url'])) ?>')" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded mr-2">Modifier</button>
                            <form action="<?= BASE_URL ?>/?action=admin_activities" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="action_type" value="delete">
                                <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editActivity(id, nom, type, description, prix, capacite, duree, image) {
    document.getElementById('addForm').classList.remove('hidden');
    document.getElementById('action_type').value = 'update';
    document.getElementById('update_id').value = id;
    
    document.getElementById('nom').value = nom;
    document.getElementById('type').value = type;
    document.getElementById('description').value = description;
    document.getElementById('prix').value = prix;
    document.getElementById('capacite_max').value = capacite;
    document.getElementById('duree').value = duree;
    
    // Pour l'image
    document.getElementById('existing_image').value = image;
    document.getElementById('current_image_text').classList.remove('hidden');
    // On ne peut pas pré-remplir un input type="file" pour des raisons de sécurité
    
    document.getElementById('submitBtn').textContent = 'Mettre à jour';
    document.getElementById('cancelBtn').classList.remove('hidden');
    
    // Scroll smoothly to form
    document.getElementById('addForm').scrollIntoView({ behavior: 'smooth' });
}

function cancelEdit() {
    document.getElementById('activityForm').reset();
    document.getElementById('action_type').value = 'add';
    document.getElementById('update_id').value = '';
    document.getElementById('existing_image').value = '';
    document.getElementById('current_image_text').classList.add('hidden');
    
    document.getElementById('submitBtn').textContent = 'Ajouter l\'activité';
    document.getElementById('cancelBtn').classList.add('hidden');
    document.getElementById('addForm').classList.add('hidden');
}
</script>

<?php require 'views/layout/footer.php'; ?>
