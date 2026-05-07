<?php require 'views/layout/header.php'; ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <a href="<?= BASE_URL ?>/?action=marketing_dashboard"
       class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-800 text-sm font-medium mb-6 group">
        <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Retour au dashboard
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-calendar-plus text-brand-500"></i>
            Créer un Nouvel Événement
        </h1>
        <p class="text-gray-500 text-sm mb-8">Renseignez les informations de votre événement ou promotion.</p>

        <form action="<?= BASE_URL ?>/?action=marketing_create" method="POST"
              enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="titre" required placeholder="Ex: Soirée DJ Cosmos Beach..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 focus:ring-1 focus:ring-brand-400 outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Décrivez l'événement..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 outline-none resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date de début <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="date_debut" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date de fin <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="date_fin" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Prix du ticket (FC) <span class="text-red-500">*</span></label>
                    <input type="number" name="prix_ticket" min="0" step="100" value="0" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Capacité max (places) <span class="text-red-500">*</span></label>
                    <input type="number" name="capacite_max" min="1" value="100" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Lieu</label>
                    <input type="text" name="lieu" placeholder="Ex: Cosmos Beach — Piscine VIP"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Type d'événement</label>
                    <select name="type_event" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-brand-400 outline-none bg-white">
                        <option value="concert">🎵 Concert</option>
                        <option value="soiree">🎉 Soirée</option>
                        <option value="sport">⚽ Sport</option>
                        <option value="promotion">🏷️ Promotion</option>
                        <option value="autre" selected>📌 Autre</option>
                    </select>
                </div>

                <!-- Upload image -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-image mr-1 text-brand-500"></i>
                        Image de l'événement <span class="text-gray-400 font-normal">(optionnel · JPG, PNG, WEBP · max 5 Mo)</span>
                    </label>

                    <!-- Zone drag & drop -->
                    <div id="dropZone"
                         onclick="document.getElementById('event_image').click()"
                         class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-brand-400 hover:bg-brand-50 transition-all duration-200 group">

                        <!-- Aperçu image -->
                        <div id="imagePreview" class="hidden mb-4">
                            <img id="previewImg" src="" alt="Aperçu"
                                 class="mx-auto max-h-52 rounded-xl object-cover shadow-md border border-gray-200">
                        </div>

                        <!-- Icône & texte par défaut -->
                        <div id="uploadPlaceholder">
                            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-100 transition-colors">
                                <i class="fa-solid fa-cloud-arrow-up text-brand-400 text-3xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 mb-1">
                                Cliquez pour choisir une photo
                            </p>
                            <p class="text-xs text-gray-400">
                                ou glissez-déposez l'image ici — depuis votre <strong>PC</strong> ou <strong>téléphone</strong>
                            </p>
                        </div>

                        <p id="fileName" class="hidden mt-3 text-xs text-brand-700 font-semibold bg-brand-50 px-3 py-1.5 rounded-full inline-block"></p>
                    </div>

                    <input type="file" id="event_image" name="event_image"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           class="hidden"
                           onchange="previewImage(this)">

                    <button type="button" id="removeImage"
                            class="hidden mt-2 text-xs text-red-500 hover:text-red-700 font-medium transition"
                            onclick="removePreview()">
                        <i class="fa-solid fa-xmark mr-1"></i> Supprimer l'image sélectionnée
                    </button>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="<?= BASE_URL ?>/?action=marketing_dashboard"
                   class="flex-1 py-3 text-center border border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="flex-1 py-3 cb-btn-primary rounded-xl font-bold text-sm shadow flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Publier l'événement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        alert('❌ L\'image dépasse 5 Mo. Choisissez une image plus petite.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('imagePreview').classList.remove('hidden');
        document.getElementById('uploadPlaceholder').classList.add('hidden');
        document.getElementById('fileName').textContent = '📎 ' + file.name;
        document.getElementById('fileName').classList.remove('hidden');
        document.getElementById('removeImage').classList.remove('hidden');
        document.getElementById('dropZone').classList.add('border-brand-400', 'bg-brand-50');
    };
    reader.readAsDataURL(file);
}

function removePreview() {
    document.getElementById('event_image').value = '';
    document.getElementById('previewImg').src = '';
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    document.getElementById('fileName').classList.add('hidden');
    document.getElementById('removeImage').classList.add('hidden');
    document.getElementById('dropZone').classList.remove('border-brand-400', 'bg-brand-50');
}

// Drag & drop
const dz = document.getElementById('dropZone');
['dragenter', 'dragover'].forEach(e => dz.addEventListener(e, ev => {
    ev.preventDefault();
    dz.classList.add('border-brand-500', 'bg-brand-50', 'scale-[1.01]');
}));
['dragleave', 'drop'].forEach(e => dz.addEventListener(e, ev => {
    ev.preventDefault();
    dz.classList.remove('scale-[1.01]');
}));
dz.addEventListener('drop', e => {
    e.preventDefault();
    const input = document.getElementById('event_image');
    if (e.dataTransfer.files.length > 0) {
        // Créer un DataTransfer pour assigner les fichiers
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        input.files = dt.files;
        previewImage(input);
    }
});
</script>

<?php require 'views/layout/footer.php'; ?>
