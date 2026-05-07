<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Cosmos Beach' : 'Cosmos Beach — Gestion & Réservation' ?></title>

    <!-- ══════════════════════════════════════════════
         FAVICON — logo Cosmos Beach dans l'onglet
    ══════════════════════════════════════════════ -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="<?= BASE_URL ?>/img/logo.png">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/img/logo.png">

    <!-- ══════════════════════════════════════════════
         TAILWIND + PALETTE UNIFIÉE COSMOS BEACH
         Couleurs tirées du logo : sky-500 → indigo-600
         Partout où il y avait green/violet/teal aléatoires,
         on utilise maintenant brand (sky) + accent (indigo)
    ══════════════════════════════════════════════ -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Couleur principale : Bleu ciel (du logo)
                        brand: {
                            50:  '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        // Couleur accent : Indigo violet (du logo)
                        accent: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        // Succès : toujours emerald (pas green aléatoire)
                        cb_success: {
                            50:  '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        // Danger : rouge uniforme
                        cb_danger: {
                            50:  '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                        },
                        // Warning : amber
                        cb_warning: {
                            50:  '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'slide-in':  'slideIn 0.3s ease-out',
                        'slide-out': 'slideOut 0.3s ease-out',
                        'fade-in':   'fadeIn 0.3s ease-out',
                    },
                    keyframes: {
                        slideIn:  { '0%': { transform: 'translateX(100%)', opacity: '0' }, '100%': { transform: 'translateX(0)', opacity: '1' } },
                        slideOut: { '0%': { transform: 'translateX(0)', opacity: '1' },    '100%': { transform: 'translateX(100%)', opacity: '0' } },
                        fadeIn:   { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ── Palette CSS variables (utilisable partout dans le site) ── */
        :root {
            --cb-sky:     #0ea5e9;
            --cb-sky-dk:  #0284c7;
            --cb-indigo:  #4f46e5;
            --cb-indigo-dk: #4338ca;
            --cb-success: #10b981;
            --cb-danger:  #ef4444;
            --cb-warning: #f59e0b;
            --cb-bg:      #f8fafc;
            --cb-card:    #ffffff;
            --cb-border:  #e2e8f0;
            --cb-text:    #1e293b;
            --cb-muted:   #64748b;
        }

        /* Dégradé principal uniforme (utilisé partout : headers de cards, boutons primaires) */
        .cb-gradient        { background: linear-gradient(135deg, var(--cb-sky), var(--cb-indigo)); }
        .cb-gradient-text   { background: linear-gradient(135deg, var(--cb-sky), var(--cb-indigo)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .cb-btn-primary     { background: linear-gradient(135deg, var(--cb-sky), var(--cb-indigo)); color: white; transition: opacity .2s, transform .1s; }
        .cb-btn-primary:hover { opacity: .9; transform: translateY(-1px); }

        /* Animations menu */
        .menu-open  { animation: slideIn  0.3s ease-out forwards; }
        .menu-close { animation: slideOut 0.3s ease-out forwards; }
        .overlay-open  { animation: fadeIn  0.3s ease-out forwards; }
        .overlay-close { animation: fadeOut 0.3s ease-out forwards; }

        @keyframes fadeOut { 0% { opacity: 1; } 100% { opacity: 0; } }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 flex flex-col min-h-screen">

    <!-- ══════════════════════════════════════════════
         NAVIGATION
    ══════════════════════════════════════════════ -->
    <nav class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="<?= BASE_URL ?>/" class="flex items-center gap-2.5 flex-shrink-0 group">
                    <img src="<?= BASE_URL ?>/img/logo.png"
                         alt="Cosmos Beach"
                         class="h-9 w-9 rounded-lg object-cover shadow-sm group-hover:scale-105 transition-transform">
                    <span class="text-xl font-bold cb-gradient-text hidden sm:block">Cosmos Beach</span>
                    <span class="text-xl font-bold cb-gradient-text sm:hidden">CB</span>
                </a>

                <!-- Nav desktop -->
                <div class="hidden md:flex md:items-center md:gap-6">
                    <?php
                    $action = $_GET['action'] ?? 'home';
                    $activeClass   = 'text-brand-600 border-b-2 border-brand-500 px-1 pt-1 text-sm font-semibold';
                    $inactiveClass = 'text-gray-500 hover:text-brand-600 border-b-2 border-transparent hover:border-brand-300 px-1 pt-1 text-sm font-medium transition-colors';
                    ?>
                    <a href="<?= BASE_URL ?>/"                    class="<?= ($action === 'home')                                                   ? $activeClass : $inactiveClass ?>">Accueil</a>
                    <a href="<?= BASE_URL ?>/?action=activities"  class="<?= in_array($action, ['activities','activity_details'])                    ? $activeClass : $inactiveClass ?>">Activités</a>
                    <a href="<?= BASE_URL ?>/?action=events"      class="<?= ($action === 'events')                                                  ? $activeClass : $inactiveClass ?>">Événements</a>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>/?action=dashboard"
                           class="<?= in_array($action, ['dashboard','admin_activities','reports','marketing_dashboard','mon_historique','marketing_create','marketing_edit','marketing_interactions']) ? $activeClass : $inactiveClass ?>">
                           Mon Espace
                        </a>
                        <?php if (($_SESSION['user_role'] ?? '') === 'VISITEUR'): ?>
                            <a href="<?= BASE_URL ?>/?action=my_event_tickets"
                               class="<?= $action === 'my_event_tickets' ? $activeClass : $inactiveClass ?>">
                                <i class="fa-solid fa-ticket mr-1 text-xs"></i> Mes Tickets
                            </a>
                        <?php endif; ?>
                        <?php if (($_SESSION['user_role'] ?? '') === 'MARKETEUR'): ?>
                            <a href="<?= BASE_URL ?>/?action=marketing_create"
                               class="<?= $action === 'marketing_create' ? $activeClass : $inactiveClass ?>">
                                <i class="fa-solid fa-plus-circle mr-1 text-xs"></i> Publier
                            </a>
                        <?php endif; ?>
                        <a href="#" onclick="confirmLogout(event)"
                           class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-medium border border-brand-200 text-brand-700 bg-brand-50 hover:bg-brand-100 transition-colors cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i> Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/?action=login"
                           class="inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-sm font-semibold text-white cb-btn-primary shadow-sm">
                            <i class="fa-solid fa-user text-xs"></i> Se Connecter
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Bouton hamburger mobile -->
                <button id="menuToggle"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <svg id="hamburgerIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Menu mobile -->
            <div id="mobileMenu" class="hidden md:hidden overflow-hidden max-h-96 transition-all duration-300">
                <div class="px-2 pt-2 pb-4 space-y-1 border-t border-gray-100">
                    <?php
                    $activeMobileClass   = 'flex items-center gap-2 text-brand-700 bg-brand-50 px-3 py-2.5 rounded-lg text-sm font-semibold w-full';
                    $inactiveMobileClass = 'flex items-center gap-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 px-3 py-2.5 rounded-lg text-sm font-medium w-full transition-colors';
                    ?>
                    <a href="<?= BASE_URL ?>/"                   class="<?= ($action==='home')                                  ? $activeMobileClass : $inactiveMobileClass ?>"><i class="fa-solid fa-house w-4"></i>Accueil</a>
                    <a href="<?= BASE_URL ?>/?action=activities" class="<?= in_array($action,['activities','activity_details']) ? $activeMobileClass : $inactiveMobileClass ?>"><i class="fa-solid fa-person-swimming w-4"></i>Activités</a>
                    <a href="<?= BASE_URL ?>/?action=events"     class="<?= ($action==='events')                                ? $activeMobileClass : $inactiveMobileClass ?>"><i class="fa-solid fa-calendar w-4"></i>Événements</a>

                    <div class="border-t border-gray-200 my-1"></div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>/?action=dashboard"
                           class="<?= in_array($action,['dashboard','admin_activities','reports','marketing_dashboard','mon_historique','marketing_create','marketing_edit','marketing_interactions']) ? $activeMobileClass : $inactiveMobileClass ?>">
                            <i class="fa-solid fa-gauge w-4"></i>Mon Espace
                        </a>
                        <?php if (($_SESSION['user_role'] ?? '') === 'VISITEUR'): ?>
                            <a href="<?= BASE_URL ?>/?action=my_event_tickets"
                               class="<?= $action === 'my_event_tickets' ? $activeMobileClass : $inactiveMobileClass ?>">
                                <i class="fa-solid fa-ticket w-4"></i>Mes Tickets
                            </a>
                        <?php endif; ?>
                        <?php if (($_SESSION['user_role'] ?? '') === 'MARKETEUR'): ?>
                            <a href="<?= BASE_URL ?>/?action=marketing_create"
                               class="<?= $action === 'marketing_create' ? $activeMobileClass : $inactiveMobileClass ?>">
                                <i class="fa-solid fa-plus-circle w-4"></i>Publier Événement
                            </a>
                        <?php endif; ?>
                        <a href="#" onclick="confirmLogout(event)"
                           class="flex items-center gap-2 text-brand-700 bg-brand-50 hover:bg-brand-100 px-3 py-2.5 rounded-lg text-sm font-medium w-full transition-colors cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket w-4"></i>Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/?action=login"
                           class="flex items-center gap-2 text-white cb-btn-primary px-3 py-2.5 rounded-lg text-sm font-semibold w-full">
                            <i class="fa-solid fa-user w-4"></i>Se Connecter
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div id="menuOverlay" class="hidden fixed inset-0 bg-black/30 md:hidden z-40 transition-opacity duration-300"></div>

    <!-- ══════════════════════════════════════════════
         MESSAGES FLASH GLOBAUX (success / error)
         Affichés sur toutes les pages automatiquement
    ══════════════════════════════════════════════ -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
    <div id="flash-success"
         class="fixed top-20 left-1/2 -translate-x-1/2 z-[999] w-full max-w-md mx-4 px-5 py-3 bg-emerald-50 border border-emerald-200 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in">
        <i class="fa-solid fa-circle-check text-emerald-500 text-lg flex-shrink-0"></i>
        <p class="text-sm font-medium text-emerald-800 flex-1"><?= htmlspecialchars($_SESSION['flash_success']) ?></p>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 ml-2">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
    <script>setTimeout(()=>{ const el=document.getElementById('flash-success'); if(el) el.remove(); }, 5000);</script>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
    <div id="flash-error"
         class="fixed top-20 left-1/2 -translate-x-1/2 z-[999] w-full max-w-md mx-4 px-5 py-3 bg-red-50 border border-red-200 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in">
        <i class="fa-solid fa-circle-exclamation text-red-500 text-lg flex-shrink-0"></i>
        <p class="text-sm font-medium text-red-800 flex-1"><?= htmlspecialchars($_SESSION['flash_error']) ?></p>
        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 ml-2">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
    <script>setTimeout(()=>{ const el=document.getElementById('flash-error'); if(el) el.remove(); }, 6000);</script>
    <?php endif; ?>

    <main class="flex-grow">

    <!-- ══════════════════════════════════════════════
         SCRIPTS GLOBAUX : MENU + DÉCONNEXION
    ══════════════════════════════════════════════ -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle   = document.getElementById('menuToggle');
        const mobileMenu   = document.getElementById('mobileMenu');
        const menuOverlay  = document.getElementById('menuOverlay');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const closeIcon    = document.getElementById('closeIcon');
        let isMenuOpen = false;

        function openMenu() {
            isMenuOpen = true;
            mobileMenu.classList.remove('hidden');
            menuOverlay.classList.remove('hidden');
            hamburgerIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            mobileMenu.classList.add('menu-open');
            menuOverlay.classList.add('overlay-open');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            isMenuOpen = false;
            mobileMenu.classList.add('menu-close');
            menuOverlay.classList.add('overlay-close');
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
                menuOverlay.classList.add('hidden');
                mobileMenu.classList.remove('menu-open','menu-close');
                menuOverlay.classList.remove('overlay-open','overlay-close');
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        menuToggle.addEventListener('click', () => isMenuOpen ? closeMenu() : openMenu());
        menuOverlay.addEventListener('click', closeMenu);
        mobileMenu.querySelectorAll('a:not([onclick])').forEach(l => l.addEventListener('click', closeMenu));
        window.addEventListener('resize', () => { if (window.innerWidth >= 768 && isMenuOpen) closeMenu(); });
    });

    // ── Déconnexion avec confirmation ──────────────────────────
    function confirmLogout(event) {
        event.preventDefault();
        const modal = document.createElement('div');
        modal.id = 'logoutModal';
        modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-right-from-bracket text-amber-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Déconnexion</h3>
                <p class="text-gray-500 text-sm mb-7">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                <div class="flex gap-3">
                    <button onclick="cancelLogout()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm">
                        Annuler
                    </button>
                    <button onclick="executeLogout()"
                            class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-colors text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-right-from-bracket"></i> Déconnexion
                    </button>
                </div>
            </div>`;
        document.body.appendChild(modal);
        modal.addEventListener('click', e => { if (e.target === modal) cancelLogout(); });
    }

    function cancelLogout() {
        const m = document.getElementById('logoutModal');
        if (m) m.remove();
    }

    function executeLogout() {
        cancelLogout();
        // Overlay de déconnexion
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-[9999] flex flex-col items-center justify-center';
        overlay.style.background = 'linear-gradient(135deg, #0ea5e9, #4f46e5)';
        overlay.innerHTML = `
            <style>
                @keyframes cb-spin { to { transform: rotate(360deg); } }
                .cb-spin { animation: cb-spin 1.2s linear infinite; }
            </style>
            <div class="relative w-20 h-20 mb-7">
                <div class="absolute inset-0 rounded-full border-4 border-white/20 border-t-white cb-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-right-from-bracket text-white text-2xl"></i>
                </div>
            </div>
            <p class="text-white text-xl font-semibold mb-1">Au revoir !</p>
            <p class="text-white/70 text-sm">Déconnexion en cours…</p>`;
        document.body.appendChild(overlay);
        setTimeout(() => { window.location.href = '<?= BASE_URL ?>/?action=logout'; }, 1800);
    }

    // Animations CSS globales
    const _s = document.createElement('style');
    _s.textContent = `
        @keyframes fadeIn  { from { opacity:0; } to { opacity:1; } }
        @keyframes fadeOut { from { opacity:1; } to { opacity:0; } }
        .animate-fade-in   { animation: fadeIn  0.3s ease-out; }
    `;
    document.head.appendChild(_s);
    </script>
