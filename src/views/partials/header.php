<style>
/* Modern Floating Navbar Styles */
.navbar-floating {
    position: fixed;
    top: 24px;
    left: 50%;
     transform: translateX(-50%) translateY(-150%); /* Hidden initially to prevent FOUC */
    opacity: 0;
    width: 95%;
    max-width: 1000px;
    background: rgba(15, 15, 20, 0.7);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 100px;
    padding: 0.8rem 1.75rem;
    z-index: 10002;
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5);
    transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    /* Animation gérée par JS pour ne s'afficher qu'une seule fois */
}

/* Light Mode Overrides for Navbar */
[data-theme="light"] .navbar-floating {
    background: rgba(255, 255, 255, 0.75);
    border-color: rgba(99, 102, 241, 0.15); /* Slight Indigo border */
    box-shadow: 0 10px 40px -10px rgba(99, 102, 241, 0.15); /* Tinted shadow */
}

[data-theme="light"] .nav-link-custom {
    color: rgba(0, 0, 0, 0.65);
}

[data-theme="light"] .nav-link-custom:hover, 
[data-theme="light"] .nav-link-custom.active {
    color: #4f46e5; /* Primary color */
    background: rgba(99, 102, 241, 0.1);
}

[data-theme="light"] .logo-text {
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

[data-theme="light"] .hamburger-line {
    background-color: #1e1e2e;
}

[data-theme="light"] #avatarBtn {
    color: #1e1e2e !important;
}

[data-theme="light"] #avatarBtn img {
    border-color: rgba(0,0,0,0.1) !important;
}

/* State when navbar is fully visible (added by JS) */
.navbar-floating.visible {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
}

.navbar-floating.animate-entry {
    animation: navSlideDown 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; /* Smoother & ensure it stays */
}

/* Classe ajoutée via JS après l'intro pour activer les transitions */
.navbar-floating.scrolling {
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), 
                opacity 0.4s ease,
                background-color 0.4s ease,
                box-shadow 0.4s ease;
    will-change: transform, opacity;
}

.navbar-floating.navbar-hidden {
    /* Remonte plus haut, réduit légèrement ma taille et devient transparent */
    transform: translateX(-50%) translateY(-140%) scale(0.96);
    opacity: 0;
    pointer-events: none;
    box-shadow: none;
}

@keyframes navSlideDown {
    from { 
        opacity: 0; 
        transform: translateX(-50%) translateY(-100%) scale(0.9); 
    }
    to { 
        opacity: 1; 
        transform: translateX(-50%) translateY(0) scale(1); 
    }
}


/* Links */
.nav-link-custom {
    color: rgba(255, 255, 255, 0.9); /* More visible by default */
    font-weight: 500;
    padding: 0.6rem 1.2rem;
    border-radius: 30px;
    transition: all 0.25s ease;
    text-decoration: none;
    font-size: 0.95rem;
    white-space: nowrap;
}

.nav-link-custom:hover, .nav-link-custom.active {
    color: white;
    background: rgba(255, 255, 255, 0.15);
}

/* Logo */
.logo-container {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.logo-text {
    font-weight: 700;
    font-size: 1.25rem;
    background: linear-gradient(to right, #fff, #cbd5e1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
}

/* Buttons */
.btn-glass {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.6rem 1.4rem;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-glass:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
    color: white;
}

.btn-gradient {
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    border: none;
    color: white;
    padding: 0.6rem 1.4rem;
    border-radius: 30px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
    color: white;
}

.btn-gradient i {
    color: white !important;
    opacity: 1 !important;
    text-shadow: none;
}


@media (max-width: 768px) {
    .btn-gradient {
        padding: 0.4rem 1rem;
        font-size: 0.9rem;
    }

    .header-login-btn {
        display: none !important;
    }
}


/* Mobile Menu */
.mobile-menu-overlay {
    position: fixed;
    inset: 0;
    background: rgba(5, 5, 10, 0.98);
    backdrop-filter: blur(20px);
    z-index: 10000;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    clip-path: circle(0% at 90% 10%);
}

.mobile-menu-overlay.active {
    opacity: 1;
    visibility: visible;
    clip-path: circle(150% at 90% 10%);
}

/* Light Mode - Mobile Menu */
[data-theme="light"] .mobile-menu-overlay {
    background: rgba(255, 255, 255, 0.98);
}

[data-theme="light"] .mobile-nav-link {
    color: rgba(30, 30, 46, 0.7);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

[data-theme="light"] .mobile-nav-link:hover, 
[data-theme="light"] .mobile-nav-link.active {
    color: #4f46e5;
    border-color: rgba(79, 70, 229, 0.3);
}

[data-theme="light"] .mobile-nav-link i {
    color: #6366f1; /* Primary indigo */
}

[data-theme="light"] .mobile-nav-link:hover i, 
[data-theme="light"] .mobile-nav-link.active i {
    text-shadow: 0 0 10px rgba(99, 102, 241, 0.4);
}
/* End Light Mode Mobile Menu */

.mobile-nav-link {
    display: flex;
    align-items: center;
    font-size: 1.25rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(20px);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.mobile-nav-link:hover, .mobile-nav-link.active {
    color: white;
    transform: translateX(10px);
    border-color: rgba(168, 85, 247, 0.3);
}

.mobile-nav-link i {
    width: 32px;
    text-align: center;
    font-size: 1.1rem;
    opacity: 0.5;
    transition: all 0.3s ease;
    margin-right: 12px;
    color: #a855f7;
}

.mobile-nav-link:hover i, .mobile-nav-link.active i {
    opacity: 1;
    text-shadow: 0 0 10px rgba(168, 85, 247, 0.5);
}

.navbar-floating.nav-transparent {
    background: transparent !important;
    backdrop-filter: none !important;
    border-color: transparent !important;
    box-shadow: none !important;
}

.mobile-menu-overlay.active .mobile-nav-link {
    opacity: 1;
    transform: translateY(0);
}

/* Stagger Animation delay */
.mobile-menu-overlay.active .mobile-nav-link:nth-child(1) { transition-delay: 0.1s; }
.mobile-menu-overlay.active .mobile-nav-link:nth-child(2) { transition-delay: 0.15s; }
.mobile-menu-overlay.active .mobile-nav-link:nth-child(3) { transition-delay: 0.2s; }
.mobile-menu-overlay.active .mobile-nav-link:nth-child(4) { transition-delay: 0.25s; }
.mobile-menu-overlay.active .mobile-nav-link:nth-child(5) { transition-delay: 0.3s; }
.mobile-menu-overlay.active .mobile-nav-link:nth-child(6) { transition-delay: 0.35s; }
.mobile-menu-overlay.active .mobile-nav-link:nth-child(7) { transition-delay: 0.4s; }

/* Hamburger Button */
.hamburger-btn {
    width: 40px;
    height: 40px;
    position: relative;
    border: none;
    background: transparent;
    cursor: pointer;
    z-index: 10001; /* Above overlay */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 0;
}

.hamburger-line {
    width: 24px;
    height: 2px;
    background-color: white;
    margin: 3px 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    transform-origin: center;
}

/* Hamburger Animation State */
.hamburger-btn.active .hamburger-line:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
}

.hamburger-btn.active .hamburger-line:nth-child(2) {
    opacity: 0;
    transform: scaleX(0);
}

.hamburger-btn.active .hamburger-line:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
}


/* User Dropdown */
.user-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 1rem;
    background: #1a1a24;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 1.5rem;
    padding: 1rem;
    width: 260px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    z-index: 10000;
    display: none;
}

/* Light Mode User Dropdown */
[data-theme="light"] .user-dropdown-menu {
    background: #ffffff;
    border-color: rgba(0,0,0,0.1);
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

[data-theme="light"] .user-dropdown-menu .text-white {
    color: #1f2937 !important;
}

[data-theme="light"] .user-dropdown-menu .border-secondary {
    border-color: #e5e7eb !important;
}

.user-dropdown-menu.show {
    display: block;
    animation: fadeIn 0.2s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (min-width: 992px) {
    .navbar-floating {
        width: 85%; /* Slightly smaller on large screens */
    }
}

.desktop-nav {
    display: none;
}

@media (min-width: 768px) {
    .desktop-nav {
        display: block;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
}
/* Theme Toggle Button */
.theme-toggle-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--foreground, #fff);
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    transition: background-color 0.3s ease;
}

.theme-toggle-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.sun-and-moon > :is(.moon, .sun, .sun-beams) {
    transform-origin: center center;
}

.sun-and-moon > :is(.moon, .sun) {
    fill: currentColor;
}

.sun-and-moon > .sun-beams {
    stroke: currentColor;
    stroke-width: 2px;
}

/* Dark Mode (Default) State - Show Moon */
/* Moon means beams are invisible, and mask creates crescent */
.sun-and-moon > .sun-beams {
    opacity: 0;
    transform: rotate(0deg);
    transition: transform 0.5s ease, opacity 0.5s ease;
}

.sun-and-moon > .moon > circle {
    transform: translateX(-7px);
    transition: transform 0.25s ease-out;
}

.sun-and-moon > .sun {
    transform: scale(1);
    transition: transform 0.5s ease;
}

/* Light Mode State - Show Sun */
[data-theme="light"] .theme-toggle-btn {
    color: var(--foreground); 
}

[data-theme="light"] .sun-and-moon > .sun-beams {
    opacity: 1;
    transform: rotate(90deg);
}

[data-theme="light"] .sun-and-moon > .moon > circle {
    transform: translateX(30px); /* Move mask completely out of way */
}

[data-theme="light"] .sun-and-moon > .sun {
    transform: scale(1);
}

</style>

<?php 
$currentUri = $_SERVER['REQUEST_URI'];
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';

function isNavLinkActive($uri, $base, $path) {
    if ($path === '' || $path === '/') {
        return $uri === $base || $uri === $base . '/'; 
    }
    return strpos($uri, $base . $path) !== false;
}
?>

<header class="navbar-floating">
    <div class="d-flex align-items-center justify-content-between position-relative">
        
        <!-- Brand -->
        <a href="<?= BASE_URL ?>" class="logo-container">
            <div class="logo-icon">
                <i class="fas fa-cube"></i>
            </div>
            <span class="logo-text d-none d-sm-block"><?= APP_NAME ?></span>
            <span class="logo-text d-sm-none">CS</span>
        </a>
        
        <!-- Desktop Nav -->
        <nav class="desktop-nav">
            <ul class="d-flex align-items-center gap-3 list-unstyled m-0">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="<?= BASE_URL ?>" class="nav-link-custom fw-bold <?= isNavLinkActive($currentUri, $basePath, '') ? 'active' : '' ?>">Accueil</a></li>
                <?php endif; ?>
                <li><a href="<?= BASE_URL ?>/offres" class="nav-link-custom fw-bold <?= isNavLinkActive($currentUri, $basePath, '/offres') ? 'active' : '' ?>">Offres</a></li>
                <li><a href="<?= BASE_URL ?>/entreprises" class="nav-link-custom fw-bold <?= isNavLinkActive($currentUri, $basePath, '/entreprises') ? 'active' : '' ?>">Entreprises</a></li>
                
                <?php if (isset($_SESSION['user_role'])): ?>
                    <?php if ($_SESSION['user_role'] === 'etudiant'): ?>
                        <li><a href="<?= BASE_URL ?>/wishlist" class="nav-link-custom <?= isNavLinkActive($currentUri, $basePath, '/wishlist') ? 'active' : '' ?>">Ma Liste</a></li>
                        <li><a href="<?= BASE_URL ?>/candidatures/etudiant" class="nav-link-custom <?= isNavLinkActive($currentUri, $basePath, '/candidatures') ? 'active' : '' ?>">Candidatures</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'pilote'): ?>
                        <li><a href="<?= BASE_URL ?>/etudiants" class="nav-link-custom <?= isNavLinkActive($currentUri, $basePath, '/etudiants') ? 'active' : '' ?>">Mes Étudiants</a></li>
                        <li><a href="<?= BASE_URL ?>/candidatures/pilote" class="nav-link-custom <?= isNavLinkActive($currentUri, $basePath, '/candidatures') ? 'active' : '' ?>">Candidatures</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="<?= BASE_URL ?>/etudiants" class="nav-link-custom <?= isNavLinkActive($currentUri, $basePath, '/etudiants') ? 'active' : '' ?>">Étudiants</a></li>
                        <li><a href="<?= BASE_URL ?>/pilotes" class="nav-link-custom <?= isNavLinkActive($currentUri, $basePath, '/pilotes') ? 'active' : '' ?>">Pilotes</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
        
        <!-- Actions -->
        <div class="d-flex align-items-center gap-3">
            <!-- Theme Toggle -->
            <button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle Theme">
                <svg class="sun-and-moon" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24">
                    <mask class="moon" id="moon-mask">
                        <rect x="0" y="0" width="100%" height="100%" fill="white" />
                        <circle cx="24" cy="10" r="6" fill="black" />
                    </mask>
                    <circle class="sun" cx="12" cy="12" r="6" mask="url(#moon-mask)" fill="currentColor" />
                    <g class="sun-beams" stroke="currentColor">
                        <line x1="12" y1="1" x2="12" y2="3" />
                        <line x1="12" y1="21" x2="12" y2="23" />
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                        <line x1="1" y1="12" x2="3" y2="12" />
                        <line x1="21" y1="12" x2="23" y2="12" />
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                    </g>
                </svg>
            </button>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>/login" class="btn-gradient header-login-btn">
                    <span>Se connecter</span>
                    <i class="fas fa-arrow-right small"></i>
                </a>
            <?php else: ?>
                <!-- User Menu -->
                <div class="position-relative" id="userMenuContainer">
                    <button type="button" class="d-flex align-items-center gap-2 border-0 bg-transparent p-0 text-white" style="cursor: pointer;" id="avatarBtn">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_prenom'] . '+' . $_SESSION['user_nom']) ?>&background=random&color=fff" class="rounded-circle border border-2 border-white border-opacity-25" width="40" height="40" alt="Avatar">
                    </button>
                    
                    <!-- Dropdown -->
                    <div class="user-dropdown-menu" id="userDropdown">
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom border-secondary border-opacity-25">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                <?= substr($_SESSION['user_prenom'], 0, 1) ?>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-white text-truncate"><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></div>
                                <div class="small text-muted text-uppercase"><?= htmlspecialchars($_SESSION['user_role']) ?></div>
                            </div>
                        </div>
                        
                        <div class="vstack gap-2">
                            <a href="<?= BASE_URL ?>/dashboard" class="nav-link-custom w-100 d-flex align-items-center gap-2 px-3">
                                <i class="fas fa-columns opacity-50 w-5"></i> Dashboard
                            </a>
                            <a href="<?= BASE_URL ?>/profile" class="nav-link-custom w-100 d-flex align-items-center gap-2 px-3">
                                <i class="fas fa-user-circle opacity-50 w-5"></i> Mon Profil
                            </a>
                            <div class="border-top border-secondary border-opacity-25 my-1"></div>
                            <a href="<?= BASE_URL ?>/logout" class="nav-link-custom w-100 d-flex align-items-center gap-2 px-3 text-danger hover-bg-danger-soft">
                                <i class="fas fa-sign-out-alt opacity-50 w-5"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Mobile Toggle with Animated Hamburger -->
            <button class="d-md-none hamburger-btn" id="mobileMenuBtn" aria-label="Menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileOverlay">
    <!-- Close button is now handled by the hamburger button itself (z-index) -->
    
    <nav class="d-flex flex-column align-items-start gap-2 w-100 px-4" style="max-width: 400px; padding-top: 80px;">
        <a href="<?= BASE_URL ?>" class="mobile-nav-link w-100">
            <i class="fas fa-home w-5 text-center"></i> Accueil
        </a>
        <a href="<?= BASE_URL ?>/offres" class="mobile-nav-link w-100">
            <i class="fas fa-briefcase w-5 text-center"></i> Offres
        </a>
        <a href="<?= BASE_URL ?>/entreprises" class="mobile-nav-link w-100">
            <i class="fas fa-building w-5 text-center"></i> Entreprises
        </a>
        
        <?php if (isset($_SESSION['user_role'])): ?>
            <?php if ($_SESSION['user_role'] === 'etudiant'): ?>
                <a href="<?= BASE_URL ?>/wishlist" class="mobile-nav-link w-100">
                    <i class="fas fa-heart w-5 text-center"></i> Ma Liste
                </a>
                <a href="<?= BASE_URL ?>/candidatures/etudiant" class="mobile-nav-link w-100">
                    <i class="fas fa-paper-plane w-5 text-center"></i> Candidatures
                </a>
            <?php elseif ($_SESSION['user_role'] === 'pilote'): ?>
                <a href="<?= BASE_URL ?>/etudiants" class="mobile-nav-link w-100">
                    <i class="fas fa-users w-5 text-center"></i> Mes Étudiants
                </a>
                <a href="<?= BASE_URL ?>/candidatures/pilote" class="mobile-nav-link w-100">
                    <i class="fas fa-clipboard-list w-5 text-center"></i> Candidatures
                </a>
            <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/etudiants" class="mobile-nav-link w-100">
                    <i class="fas fa-user-graduate w-5 text-center"></i> Étudiants
                </a>
                <a href="<?= BASE_URL ?>/pilotes" class="mobile-nav-link w-100">
                    <i class="fas fa-chalkboard-teacher w-5 text-center"></i> Pilotes
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="d-flex gap-3 mt-4 w-100 px-3 mobile-nav-link border-0">
                <a href="<?= BASE_URL ?>/login" class="btn-gradient w-100 justify-content-center py-2">Se connecter</a>
            </div>
        <?php else: ?>
            <div class="w-100 px-3 mt-4 mobile-nav-link border-0 d-block">
                <a href="<?= BASE_URL ?>/dashboard" class="btn-gradient w-100 justify-content-center py-2 mb-3">
                    <i class="fas fa-columns me-2"></i> Mon Dashboard
                </a>
                <a href="<?= BASE_URL ?>/logout" class="d-flex align-items-center justify-content-center text-danger text-decoration-none small text-uppercase fw-bold letter-spacing-1">
                    <i class="fas fa-sign-out-alt me-2"></i> Se déconnecter
                </a>
            </div>
        <?php endif; ?>
    </nav>
</div>

<script>
    // Theme Init (Inline to prevent flash)
    (function() {
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    })();

document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle Logic
    const themeToggleBtn = document.getElementById('themeToggle');
    const root = document.documentElement;

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
             const currentTheme = root.getAttribute('data-theme');
             const newTheme = currentTheme === 'light' ? 'dark' : 'light';
             
             if (newTheme === 'dark') {
                 root.removeAttribute('data-theme'); // Go back to default (Dark)
             } else {
                 root.setAttribute('data-theme', 'light');
             }
             
             localStorage.setItem('theme', newTheme);
        });
    }

    // User Dropdown Logic
    const avatarBtn = document.getElementById('avatarBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (avatarBtn && userDropdown) {
        avatarBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (userDropdown.classList.contains('show')) {
                if (!avatarBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
            }
        });
    }

    // Mobile Menu Logic
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    // const closeMenuBtn = document.getElementById('closeMenuBtn'); // Removed close button, main button toggles
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    if (mobileMenuBtn && mobileOverlay) {
        function toggleMenu() {
            mobileOverlay.classList.toggle('active');
            mobileMenuBtn.classList.toggle('active'); // Toggle hamburger animation
            document.body.style.overflow = mobileOverlay.classList.contains('active') ? 'hidden' : '';
        }

        mobileMenuBtn.addEventListener('click', toggleMenu);
        
        // Removed separate close button event listener
        // if(closeMenuBtn) closeMenuBtn.addEventListener('click', toggleMenu);

        // Close menu when clicking a link
        mobileOverlay.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', toggleMenu);
        });
    }
    // Scroll Hide/Show Logic
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar-floating');
    
    // GESTION DE L'ANIMATION D'ENTRÉE (Une seule fois par session)
    if (!sessionStorage.getItem('nav_animated')) {
        // C'est la première visite, on joue l'animation
        navbar.classList.add('animate-entry');
        sessionStorage.setItem('nav_animated', 'true');
        
        // Après l'animation, on active les transitions de scroll normales
        setTimeout(() => {
            navbar.classList.remove('animate-entry');
            navbar.classList.add('visible');
            navbar.classList.add('scrolling');
        }, 800);
    } else {
        // Déjà visité, pas d'animation d'entrée, on active direct le scroll behavior
        navbar.classList.add('visible');
        navbar.classList.add('scrolling');
    }

    window.addEventListener('scroll', () => {
        // Force l'activation si scroll avant la fin du timer
        if (!navbar.classList.contains('scrolling')) {
            navbar.classList.add('scrolling');
            navbar.classList.add('visible');
            navbar.style.animation = 'none';
        }

        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Downscroll & not at very top
            navbar.classList.add('navbar-hidden');
        } else {
            // Upscroll
            navbar.classList.remove('navbar-hidden');
        }
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
    }, { passive: true });
});
</script>
