<?php require 'views/layout/header.php'; ?>

<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden lg:flex">
            <!-- Image de l'activité -->
            <div class="lg:w-1/2">
                <div class="h-64 sm:h-80 lg:h-full relative">
                    <?php if($activity['image_url']): ?>
                        <img src="<?= htmlspecialchars($activity['image_url']) ?>" alt="<?= htmlspecialchars($activity['nom']) ?>" class="w-full h-full object-cover object-center">
                    <?php else: ?>
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fa-solid fa-image font-bold text-gray-400 text-6xl"></i>
                        </div>
                    <?php endif; ?>
                    <div class="absolute top-6 left-6 bg-white/90 backdrop-blur px-4 py-2 rounded-full text-sm font-bold shadow-sm text-brand-600 uppercase tracking-widest">
                        <?= htmlspecialchars(str_replace('_', ' ', $activity['type'])) ?>
                    </div>
                </div>
            </div>

            <!-- Contenu et Formulaire de réservation -->
            <div class="lg:w-1/2 p-8 sm:p-12">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-4"><?= htmlspecialchars($activity['nom']) ?></h1>
                <p class="text-gray-500 text-lg mb-6 leading-relaxed">
                    <?= nl2br(htmlspecialchars($activity['description'])) ?>
                </p>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-brand-50 p-4 rounded-2xl flex items-center">
                        <div class="bg-brand-100 rounded-full p-2 text-brand-600 mr-3">
                            <i class="fa-regular fa-money-bill-1 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-brand-600 font-semibold uppercase tracking-wider">Tarif</p>
                            <p class="text-lg font-bold text-gray-900"><?= number_format($activity['prix'], 2, ',', ' ') ?> FC</p>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-50 p-4 rounded-2xl flex items-center">
                        <div class="bg-indigo-100 rounded-full p-2 text-indigo-600 mr-3">
                            <i class="fa-regular fa-clock text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wider">Durée</p>
                            <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($activity['duree']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-8 border-b pb-8 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 drop-shadow-sm">Réserver cette activité</h3>
                    
                    <?php if(isset($_SESSION['flash_error'])): ?>
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"><i class="fa-solid fa-triangle-exclamation mr-1"></i> <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-circle-info text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Vous devez être connecté pour effectuer une réservation.
                                        <a href="<?= BASE_URL ?>/?action=login" class="font-bold underline hover:text-blue-600">Se connecter</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <form action="<?= BASE_URL ?>/?action=book_activity" method="POST" class="space-y-4">
                            <input type="hidden" name="activite_id" value="<?= $activity['id'] ?>">
                            <input type="hidden" name="activite_type" value="<?= $activity['type'] ?>">
                            
                            <div>
                                <label for="date_reservation" class="block text-sm font-medium text-gray-700 mb-1">Date et heure souhaitées</label>
                                <input type="datetime-local" id="date_reservation" name="date_reservation" required min="<?= date('Y-m-d\TH:i') ?>" class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-lg py-3 px-4 bg-gray-50 border outline-none">
                            </div>

                            <?php if ($activity['type'] === 'chambre'): ?>
                                <div>
                                    <label for="nombre_personnes" class="block text-sm font-medium text-gray-700 mb-1">Nombre de personnes</label>
                                    <input type="number" id="nombre_personnes" name="nombre_personnes" value="1" min="1" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2 px-4 bg-gray-50 border outline-none" onchange="updateChambreTotal()">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode de réservation</label>
                                    <div class="flex space-x-4 mb-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="mode_reservation" value="partage" checked class="form-radio text-brand-600" onchange="updateChambreTotal()">
                                            <span class="ml-2 text-sm text-gray-700">Chambre(s) partagée(s)</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="mode_reservation" value="separe" class="form-radio text-brand-600" onchange="updateChambreTotal()">
                                            <span class="ml-2 text-sm text-gray-700">Chambres séparées</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label for="nombre_chambres" class="block text-sm font-medium text-gray-700 mb-1">Nombre de chambres</label>
                                    <input type="number" id="nombre_chambres" name="nombre_chambres" value="1" min="1" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2 px-4 bg-gray-50 border outline-none" onchange="updateChambreTotal(true)">
                                </div>

                                <div class="bg-blue-50 p-4 rounded-lg mt-4 border border-blue-100">
                                    <p class="text-sm text-blue-800">Montant total estimé : <span id="total_price" class="font-bold text-lg"><?= number_format($activity['prix'], 0, ',', ' ') ?></span> FC</p>
                                </div>

                                <script>
                                function updateChambreTotal(manualChamber = false) {
                                    const prixUnitaire = <?= $activity['prix'] ?>;
                                    const nbPersonnes = parseInt(document.getElementById('nombre_personnes').value) || 1;
                                    let nbChambresInput = document.getElementById('nombre_chambres');
                                    const mode = document.querySelector('input[name="mode_reservation"]:checked').value;
                                    
                                    if (!manualChamber) {
                                        if (mode === 'separe') {
                                            nbChambresInput.value = nbPersonnes;
                                            nbChambresInput.readOnly = true;
                                        } else {
                                            nbChambresInput.readOnly = false;
                                            // En partage, on suggère 1 chambre par défaut pour petit groupe
                                            nbChambresInput.value = Math.ceil(nbPersonnes / 2); 
                                        }
                                    }
                                    
                                    const total = prixUnitaire * (parseInt(nbChambresInput.value) || 1);
                                    document.getElementById('total_price').innerText = new Intl.NumberFormat('fr-FR').format(total);
                                }
                                </script>

                            <?php elseif ($activity['type'] === 'restaurant'): ?>
                                <div>
                                    <label for="nombre_tables" class="block text-sm font-medium text-gray-700 mb-1">Nombre de tables souhaitées</label>
                                    <input type="number" id="nombre_tables" name="nombre_tables" value="1" min="1" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2 px-4 bg-gray-50 border outline-none">
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg mt-4 border border-yellow-100 flex items-start">
                                    <i class="fa-solid fa-circle-info text-yellow-600 mt-1 mr-2"></i>
                                    <p class="text-sm text-yellow-800">Réservation de table uniquement. Le paiement de vos consommations se fera sur place.</p>
                                </div>

                            <?php elseif (in_array($activity['type'], ['piscine_ordinaire', 'piscine_vip'])): ?>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="nombre_adultes" class="block text-sm font-medium text-gray-700 mb-1">Adultes</label>
                                        <input type="number" id="nombre_adultes" name="nombre_adultes" value="1" min="0" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2 px-4 bg-gray-50 border outline-none" onchange="updatePiscineTotal()">
                                    </div>
                                    <div>
                                        <label for="nombre_enfants" class="block text-sm font-medium text-gray-700 mb-1">Enfants (Demi-tarif)</label>
                                        <input type="number" id="nombre_enfants" name="nombre_enfants" value="0" min="0" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2 px-4 bg-gray-50 border outline-none" onchange="updatePiscineTotal()">
                                    </div>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-lg mt-4 border border-blue-100">
                                    <p class="text-sm text-blue-800">Montant total estimé : <span id="total_price" class="font-bold text-lg"><?= number_format($activity['prix'], 0, ',', ' ') ?></span> FC</p>
                                </div>

                                <script>
                                function updatePiscineTotal() {
                                    const prixAdulte = <?= $activity['prix'] ?>;
                                    const prixEnfant = prixAdulte / 2;
                                    const adultes = parseInt(document.getElementById('nombre_adultes').value) || 0;
                                    const enfants = parseInt(document.getElementById('nombre_enfants').value) || 0;
                                    
                                    const total = (adultes * prixAdulte) + (enfants * prixEnfant);
                                    document.getElementById('total_price').innerText = new Intl.NumberFormat('fr-FR').format(total);
                                }
                                </script>

                            <?php else: ?>
                                <div>
                                    <label for="nombre_personnes" class="block text-sm font-medium text-gray-700 mb-1">Nombre de personnes</label>
                                    <input type="number" id="nombre_personnes" name="nombre_personnes" value="1" min="1" required class="focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2 px-4 bg-gray-50 border outline-none" onchange="updateDefaultTotal()">
                                </div>

                                <div class="bg-blue-50 p-4 rounded-lg mt-4 border border-blue-100">
                                    <p class="text-sm text-blue-800">Montant total estimé : <span id="total_price" class="font-bold text-lg"><?= number_format($activity['prix'], 0, ',', ' ') ?></span> FC</p>
                                </div>

                                <script>
                                function updateDefaultTotal() {
                                    const prixUnitaire = <?= $activity['prix'] ?>;
                                    const nbPersonnes = parseInt(document.getElementById('nombre_personnes').value) || 1;
                                    const total = prixUnitaire * nbPersonnes;
                                    document.getElementById('total_price').innerText = new Intl.NumberFormat('fr-FR').format(total);
                                }
                                </script>
                            <?php endif; ?>

                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-base font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none transition-transform transform hover:-translate-y-1 mt-6">
                                Confirmer la réservation
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
