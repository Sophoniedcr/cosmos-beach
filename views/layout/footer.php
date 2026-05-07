    </main>

    <!-- Pied de page -->
    <footer class="bg-gray-900 text-white py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fa-solid fa-umbrella-beach text-brand-500 mr-2"></i>Cosmos Beach
                    </h3>
                    <p class="text-gray-400 text-sm">
                        Votre destination de rêve pour des moments inoubliables. Profitez de nos activités, piscines VIP et hébergements de qualité.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-200">Liens Utiles</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="<?= BASE_URL ?>/" class="hover:text-brand-400 transition-colors">Accueil</a></li>
                        <li><a href="<?= BASE_URL ?>/?action=activities" class="hover:text-brand-400 transition-colors">Activités</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-200">Horaires</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>Lun-Ven : 08h00 - 22h00</li>
                        <li>Sam-Dim : 08h00 - 23h59</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
                &copy; <?= date('Y') ?> Cosmos Beach. Tous droits réservés. Système de gestion & réservation.
            </div>
        </div>
    </footer>
</body>
</html>
