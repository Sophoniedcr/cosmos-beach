<?php require 'views/layout/header.php'; ?>

<style>
    /* Ondulations fluides de la goutte d'eau (effet évasion et luxe) */
    @keyframes waterFlow {
        0%, 100% { 
            border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; 
            transform: rotate(0deg) scale(1); 
        }
        33% { 
            border-radius: 70% 30% 46% 54% / 30% 29% 71% 70%; 
            transform: rotate(120deg) scale(1.06);
        }
        66% { 
            border-radius: 100% 60% 60% 100% / 100% 100% 60% 60%; 
            transform: rotate(240deg) scale(0.94);
        }
    }

    /* Brillance dorée "Ding" qui balaie horizontalement */
    @keyframes goldenScan {
        0% { transform: skewX(-25deg) translateX(-150%); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: skewX(-25deg) translateX(150%); opacity: 0; }
    }

    /* Loader organique */
    .premium-loader {
        position: relative;
        width: 95px;
        height: 95px;
        background: linear-gradient(135deg, #0ea5e9, #4f46e5);
        animation: waterFlow 4s linear infinite;
        box-shadow: 0 10px 30px rgba(14, 165, 233, 0.4), inset 0 0 20px rgba(255, 255, 255, 0.3);
    }

    /* Trait de balayage lumineux */
    .ding-flash {
        position: fixed;
        inset: 0;
        z-index: 99998;
        background: linear-gradient(90deg, transparent, rgba(253, 224, 71, 0.3), transparent);
        width: 100vw;
        pointer-events: none;
        opacity: 0;
    }

    .run-ding .ding-flash {
        animation: goldenScan 0.9s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }
</style>

<div id="cosmos-transition-screen" style="position: fixed; inset: 0; z-index: 999999; display: none;" class="flex flex-col items-center justify-center bg-slate-950/95 backdrop-blur-md opacity-0 transition-opacity duration-500">
    <div class="ding-flash"></div>

    <div class="relative flex items-center justify-center mb-8">
        <div class="absolute h-36 w-36 rounded-full border border-sky-500/20 animate-ping" style="animation-duration: 3s;"></div>
        <div class="absolute h-28 w-28 rounded-full border border-indigo-500/40 animate-ping" style="animation-duration: 2s;"></div>
        
        <div class="premium-loader flex items-center justify-center text-white shadow-2xl">
            <i class="fa-solid fa-umbrella-beach text-3xl"></i>
        </div>
    </div>

    <h2 class="text-white text-3xl font-light tracking-[0.3em] uppercase">Cosmos Beach</h2>
    <div class="w-16 h-[2px] bg-gradient-to-r from-sky-400 to-indigo-500 my-4 rounded-full"></div>
    <p class="text-sky-200/60 text-xs tracking-[0.2em] uppercase font-light animate-pulse">Évasion en cours...</p>
</div>

<div class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32 pt-20">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Évadez-vous à</span>
                        <span class="block text-brand-600 xl:inline">Cosmos Beach</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Découvrez un cadre idyllique pour vos loisirs, événements et séjours. Mini zoo, restaurant gastronomique, chambres luxueuses et piscines VIP vous attendent.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start gap-4">
                        <div class="flex items-center justify-center h-12 w-12 rounded-full bg-brand-500 text-white shadow-md">
                            <i class="fa-solid fa-water text-xl"></i>
                        </div>
                        <a href="<?= BASE_URL ?>/?action=activities" class="w-full sm:w-auto flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-brand-600 hover:bg-brand-700 md:py-4 md:text-lg transition-transform hover:scale-105 shadow-lg">
                            Réserver une activité
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 lg:[clip-path:polygon(15%_0,100%_0,100%_100%,0_100%)] overflow-hidden">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full transform hover:scale-105 transition-transform duration-1000" src="<?= BASE_URL ?>/img/detente.jpg" alt="Plage et détente">
    </div>
</div>

<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Nos Expériences Incontournables</h2>
            <p class="mt-4 text-xl text-gray-500">Un aperçu de ce que Cosmos Beach a à vous offrir</p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
                <div class="relative h-48">
                    <img src="<?= BASE_URL ?>/img/piscine-VIP.jpg" alt="Piscine VIP" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-semibold text-brand-600">Luxe</div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Piscine VIP</h3>
                    <p class="text-gray-500 mb-4 line-clamp-2">Profitez d'un espace calme et exclusif, avec service à la place et lits balinais.</p>
                    <a href="<?= BASE_URL ?>/?action=activities" class="text-brand-600 font-medium hover:text-brand-500 inline-flex items-center">
                        Découvrir <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
                <div class="relative h-48">
                    <img src="<?= BASE_URL ?>/img/nourriture.jpg" alt="Restaurant gastronomique" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-semibold text-orange-500">Gastronomie</div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Restaurant La Corniche</h3>
                    <p class="text-gray-500 mb-4 line-clamp-2">Des plats locaux et internationaux revisités par notre chef étoilé, avec vue sur mer.</p>
                    <a href="<?= BASE_URL ?>/?action=activities" class="text-brand-600 font-medium hover:text-brand-500 inline-flex items-center">
                        Découvrir <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
                <div class="relative h-48">
                    <img src="<?= BASE_URL ?>/img/Chambre-hotel.jpg" alt="Bungalow vue mer" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-semibold text-indigo-500">Séjour</div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Chambres & Suites</h3>
                    <p class="text-gray-500 mb-4 line-clamp-2">Des hébergements confortables pour une nuit ou un long séjour relaxant.</p>
                    <a href="<?= BASE_URL ?>/?action=activities" class="text-brand-600 font-medium hover:text-brand-500 inline-flex items-center">
                        Découvrir <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnDesktop = document.getElementById('loginBtn');
    const btnMobile = document.getElementById('loginBtnMobile');
    const screen = document.getElementById('cosmos-transition-screen');

    // Fonction pour réinitialiser et cacher l'écran de transition
    function resetTransitionScreen() {
        if (screen) {
            screen.style.display = 'none';
            screen.classList.remove('run-ding', 'opacity-100');
            screen.classList.add('opacity-0');
        }
    }

    function launchPremiumTransition(event, redirectUrl) {
        event.preventDefault(); // On coupe la redirection instantanée

        if (screen) {
            screen.style.display = 'flex';

            setTimeout(() => {
                screen.classList.add('run-ding');
                screen.classList.remove('opacity-0');
                screen.classList.add('opacity-100');
            }, 30);

            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 3000);
        } else {
            window.location.href = redirectUrl;
        }
    }

    // Association de l'événement sur le bouton classique
    if (btnDesktop) {
        btnDesktop.addEventListener('click', function(e) {
            launchPremiumTransition(e, this.getAttribute('href'));
        });
    }

    // Association de l'événement sur le bouton mobile
    if (btnMobile) {
        btnMobile.addEventListener('click', function(e) {
            launchPremiumTransition(e, this.getAttribute('href'));
        });
    }

    /* ============================================================
    SECURITÉ ANTI-BLOCAGE (BOUTON RETOUR)
    ============================================================
    L'événement 'pageshow' s'exécute à chaque affichage de page,
    y compris lorsque la page est récupérée depuis le cache historique 
    (quand l'utilisateur clique sur le bouton "Retour" du navigateur).
    */
    window.addEventListener('pageshow', function (event) {
        // Si la page est chargée depuis le cache du navigateur (bouton retour cliqué)
        if (event.persisted) {
            resetTransitionScreen();
        }
    });
});
</script>

<?php require 'views/layout/footer.php'; ?>