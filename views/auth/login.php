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
    <p class="text-sky-200/60 text-xs tracking-[0.2em] uppercase font-light animate-pulse">Connexion en cours...</p>
</div>

<div class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-brand-600 to-indigo-600 p-8 text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-white text-brand-600 mb-4">
                <i class="fa-solid fa-umbrella-beach text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-white">Rebonjour !</h2>
            <p class="text-brand-100 mt-2">Connectez-vous pour gérer vos réservations.</p>
        </div>

        <div class="p-8">
            <?php if(!empty($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 relative">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-exclamation text-red-500 mr-2"></i>
                        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form id="loginForm" class="space-y-6" action="<?= BASE_URL ?>/?action=login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Adresse Email</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-50 border outline-none transition-colors" placeholder="vous@exemple.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-50 border outline-none transition-colors" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <div class="text-sm">
                        <a href="<?= BASE_URL ?>/?action=forgot_password" class="font-medium text-brand-600 hover:text-brand-500 transition-colors"> Mot de passe oublié ? </a>
                    </div>
                </div>

                <div>
                    <button id="submitBtn" type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-transform transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-2 my-auto"></i> Se connecter
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Vous n'avez pas de compte ? 
                    <a href="<?= BASE_URL ?>/?action=register" class="font-medium text-brand-600 hover:text-brand-500">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById('loginForm');
    const screen = document.getElementById('cosmos-transition-screen');

    // Réinitialise l'écran (utile lors d'un retour arrière sur le navigateur)
    function resetTransitionScreen() {
        if (screen) {
            screen.style.display = 'none';
            screen.classList.remove('run-ding', 'opacity-100');
            screen.classList.add('opacity-0');
        }
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            // Si les champs requis du navigateur sont valides, on lance l'animation
            if (loginForm.checkValidity()) {
                e.preventDefault(); // On bloque la soumission immédiate

                if (screen) {
                    screen.style.display = 'flex';

                    // Déclenche l'opacité et l'animation "Ding" de balayage doré
                    setTimeout(() => {
                        screen.classList.add('run-ding');
                        screen.classList.remove('opacity-0');
                        screen.classList.add('opacity-100');
                    }, 30);

                    // Soumet réellement le formulaire après 3 secondes
                    setTimeout(() => {
                        loginForm.submit();
                    }, 3000);
                } else {
                    loginForm.submit();
                }
            }
        });
    }

    // Sécurité : masquer l'écran si l'utilisateur revient en arrière dans l'historique
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            resetTransitionScreen();
        }
    });
});
</script>

<?php require 'views/layout/footer.php'; ?>