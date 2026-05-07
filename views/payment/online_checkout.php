<?php require 'views/layout/header.php'; ?>

<div class="bg-gray-50 py-16 min-h-[80vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="bg-indigo-900 border-b border-indigo-800 p-8 text-center">
                <i class="fa-solid fa-shield-halved text-4xl text-indigo-300 mb-4"></i>
                <h2 class="text-2xl font-bold text-white">Paiement Sécurisé en Ligne</h2>
                <p class="text-indigo-200 mt-2">Cosmos Beach - Facturation via Passerelle</p>
            </div>
            
            <div class="p-8">
                <!-- Détail de la commande -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Résumé de la commande</h3>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Activité :</span>
                        <span class="font-medium text-gray-900"><?= htmlspecialchars($reservation['activite_nom']) ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Réservation n° :</span>
                        <span class="font-medium text-gray-900">#<?= str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 mt-4 pt-4">
                        <span class="text-gray-900 font-bold">Total à Payer :</span>
                        <span class="text-2xl font-extrabold text-brand-600"><?= number_format($reservation['montant_total'], 2, ',', ' ') ?> FC</span>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/?action=process_online_payment" method="POST" class="space-y-6">
                    <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Choisissez votre moyen de paiement</h3>
                    
                    <!-- Radios Moyens de paiement -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <!-- Mobile Money -->
                        <div class="relative">
                            <input type="radio" name="methode" value="MOBILE_MONEY" id="mobile_money" class="peer hidden" checked>
                            <label for="mobile_money" class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-brand-500 peer-checked:bg-brand-50 hover:bg-gray-50 transition-all">
                                <i class="fa-solid fa-mobile-screen-button text-3xl mb-2 text-gray-700 peer-checked:text-brand-600"></i>
                                <span class="font-bold text-gray-900">Mobile Money</span>
                                <span class="text-xs text-center text-gray-500 mt-1">Wave, Orange, MTN, Moov</span>
                            </label>
                            <div class="absolute top-2 right-2 hidden peer-checked:block text-brand-500">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>

                        <!-- Carte Bancaire -->
                        <div class="relative">
                            <input type="radio" name="methode" value="CARTE" id="carte_bancaire" class="peer hidden">
                            <label for="carte_bancaire" class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-brand-500 peer-checked:bg-brand-50 hover:bg-gray-50 transition-all">
                                <i class="fa-solid fa-credit-card text-3xl mb-2 text-gray-700 peer-checked:text-brand-600"></i>
                                <span class="font-bold text-gray-900">Carte Bancaire</span>
                                <span class="text-xs text-center text-gray-500 mt-1">Visa, Mastercard</span>
                            </label>
                            <div class="absolute top-2 right-2 hidden peer-checked:block text-brand-500">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Faux Numéro pour simulation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone / Numéro de carte</label>
                        <input type="text" required placeholder="Ex: 0700000000 ou 4000 0000..." class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm py-3 px-4 border focus:ring-brand-500 focus:border-brand-500 bg-gray-50">
                        <p class="mt-2 text-xs text-gray-500 italic">* Ceci est une démo. Aucun compte ne sera réellement débité. Cliquez sur "Payer" pour simuler une réussite transactionnelle.</p>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition-transform transform hover:-translate-y-1">
                        🔒 Payer <?= number_format($reservation['montant_total'], 0, ',', ' ') ?> FC
                    </button>
                    
                    <div class="text-center mt-4">
                        <a href="<?= BASE_URL ?>/?action=dashboard" class="text-sm text-gray-500 hover:text-gray-900 underline">Annuler et retourner au tableau de bord</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
