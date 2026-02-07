<?php
$title = "Accueil";
?>

<!-- Animated Styles -->
<style>
/* Animated Background Area */
.hero-animated-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
    background: radial-gradient(circle at center, rgb(15, 15, 23) 0%, rgb(8, 8, 12) 100%);
}

.hero-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.5;
    animation: blob-bounce 20s infinite ease-in-out alternate;
}

.hero-blob:nth-child(1) {
    top: -10%;
    left: -10%;
    width: 60vw;
    height: 60vw;
    background: hsl(var(--primary) / 0.6);
}

.hero-blob:nth-child(2) {
    bottom: -10%;
    right: -20%;
    width: 70vw;
    height: 70vw;
    background: hsl(var(--secondary) / 0.6);
    animation-delay: -5s;
    animation-direction: alternate-reverse;
}

.hero-blob:nth-child(3) {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40vw;
    height: 40vw;
    background: #8b5cf6;
    opacity: 0.3;
    animation: blob-pulse 15s infinite ease-in-out;
}

@keyframes blob-bounce {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(50px, 80px) scale(1.1); }
}

@keyframes blob-pulse {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
    50% { transform: translate(-50%, -50%) scale(1.3); opacity: 0.5; }
    100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.animate-on-scroll.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Glassmorphism Cards */
.glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.glass-card:hover {
    background: rgba(255, 255, 255, 0.07);
    transform: translateY(-5px);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
}

/* Typing Effect Cursor */
.typing-cursor::after {
    content: '|';
    animation: blink 1s step-end infinite;
}

@keyframes blink { 50% { opacity: 0; } }

/* Floating Offer Cards */
.floating-offer {
    position: fixed;
    pointer-events: none;
    z-index: 1000;
    opacity: 0;
    transform: scale(0.8);
    transition: opacity 0.3s ease, transform 0.3s ease;
    width: 200px;
    background: rgba(30, 30, 40, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);
}

.floating-offer.active {
    opacity: 1;
    transform: scale(1);
}
/* Force disable hover lift on mobile */
@media (max-width: 991px) {
    .hover-lift:hover {
        transform: none !important;
        box-shadow: none !important;
    }
    .group-hover\:-translate-y-2 {
        transform: none !important;
    }
    .feature-card:hover { 
        transform: none !important; 
    }
}

/* Mobile adjustments for hero separator and layout */
@media (max-width: 768px) {
    .hero {
        min-height: auto !important; /* Allow content to dictate height */
        padding-top: 7rem !important; /* Push content down from fixed navbar */
        padding-bottom: 6rem !important;
        align-items: flex-start !important; /* Prevent vertical centering issues */
    }

    .hero h1 {
        font-size: 2.2rem !important; /* Scale down title */
        margin-bottom: 1.5rem !important;
    }

    .hero .lead {
        font-size: 1rem !important; /* Smaller font size */
        padding: 0 1.5rem; /* More horizontal padding */
        margin-bottom: 2rem !important; /* Reduce bottom spacing */
        line-height: 1.5;
    }

    /* Enhance background for mobile - making it brighter/clearer */
    .hero-animated-bg {
        background: radial-gradient(circle at 50% 30%, rgb(30, 30, 50) 0%, rgb(10, 10, 18) 100%) !important;
    }

    /* Adjust blobs to be less intrusive but visible */
    .hero-blob:nth-child(1) {
        top: 0% !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: 120vw !important;
        height: 120vw !important;
        opacity: 0.5 !important; /* Increased opacity for better visibility */
        filter: blur(80px) !important; /* Reduced blur slightly for clarity */
    }
    
    .hero-blob:nth-child(2) {
        bottom: 0% !important;
        right: -50% !important;
        width: 100vw !important;
        height: 100vw !important;
        opacity: 0.4 !important; /* Increased opacity */
        filter: blur(80px) !important;
    }

    /* Stack buttons on mobile */
    .hero .d-flex.gap-3 {
        flex-direction: column;
        width: 100%;
        padding: 0 1rem;
    }

    .hero .btn-lg {
        width: 100%;
        padding: 1rem !important;
        font-size: 1rem !important;
    }

    /* Adjust logos */
    .hero .opacity-50.grayscale {
        flex-wrap: wrap;
        gap: 1.5rem !important;
        padding-top: 2rem !important;
    }

    .hero .fab {
        font-size: 1.5rem !important;
    }
    
    .hero-separator svg {
        height: 40px !important;
    }
}

/* --- LIGHT MODE OVERRIDES --- */
[data-theme="light"] .hero-animated-bg {
    background: radial-gradient(circle at center, #ffffff 0%, #e2e8f0 100%) !important;
}

[data-theme="light"] .hero-animated-bg > div:first-child + div + div + div {
    /* Targetting the grid pattern div (4th child) */
    background-image: linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px) !important;
}

[data-theme="light"] .hero h1.text-white {
    color: #0f172a !important; /* Slate 900 */
}

[data-theme="light"] .hero .lead {
    color: #475569 !important; /* Slate 600 */
}

[data-theme="light"] .glass-card {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.1);
}

[data-theme="light"] .glass-card:hover {
    background: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 0 0 1px var(--primary);
}

[data-theme="light"] .floating-offer {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.15);
    color: #0f172a;
}

[data-theme="light"] .floating-offer h6 {
    color: #0f172a;
}

[data-theme="light"] .floating-offer p, 
[data-theme="light"] .floating-offer div {
    color: #475569 !important;
}

[data-theme="light"] .rounded-pill.bg-white\/10 {
    background: rgba(0, 0, 0, 0.05) !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
    color: #334155 !important;
}

[data-theme="light"] .hero-blob {
    opacity: 0.25; /* Softer blobs */
}

/* Features Section Light Mode Fixes */
[data-theme="light"] h2.text-white, 
[data-theme="light"] h3.text-white,
[data-theme="light"] .glass-card .text-white {
    color: #0f172a !important; /* Dark text for titles */
}

[data-theme="light"] .glass-card .text-muted {
    color: #475569 !important; /* Readable slate text for descriptions */
}

/* Fix Mockup UI elements inside feature cards */
[data-theme="light"] .glass-card .bg-opacity-5 {
    background-color: rgba(0, 0, 0, 0.05) !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
}

[data-theme="light"] .glass-card .bg-white.bg-opacity-5 {
    background-color: rgba(0, 0, 0, 0.05) !important;
}

[data-theme="light"] .glass-card .h6.text-white {
    color: #1e293b !important;
}

[data-theme="light"] .glass-card .small.text-white {
    color: #334155 !important;
}

[data-theme="light"] .glass-card .feature-mockup-bg {
    background: rgba(255, 255, 255, 0.8) !important;
    border-color: rgba(0,0,0,0.1) !important;
}

[data-theme="light"] .glass-card .rounded-4.border-white {
    border-color: rgba(0,0,0,0.1) !important;
    background: rgba(255,255,255,0.5) !important;
}
</style>

<div class="hero position-relative overflow-hidden d-flex align-items-center" style="min-height: 90vh; padding-bottom: 10rem;">
    <!-- Animated Background Layer -->
    <div class="hero-animated-bg">
        <div class="hero-blob"></div>
        <div class="hero-blob"></div>
        <div class="hero-blob"></div>
        <!-- Grid Pattern Overlay -->
        <div style="position: absolute; inset:0; background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px); background-size: 50px 50px; opacity: 0.2; mask-image: radial-gradient(circle at center, black 0%, transparent 80%); -webkit-mask-image: radial-gradient(circle at center, black 0%, transparent 80%);"></div>
    </div>

    <!-- Wave Separator -->
    <div class="hero-separator" style="position: absolute; bottom: -1px; left: 0; width: 100%; overflow: hidden; line-height: 0; z-index: 1;">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" style="position: relative; display: block; width: calc(100% + 1.3px); height: 120px;">
            <defs>
                <mask id="book-mask">
                    <rect width="100%" height="100%" fill="white" />
                    <path d="M602.45,3.86h0S572.9,116.24,281.94,120H923C632,116.24,602.45,3.86,602.45,3.86Z" fill="black" transform="rotate(180 600 60)" />
                </mask>
            </defs>
            <rect width="100%" height="100%" style="fill: hsl(var(--background));" mask="url(#book-mask)" />
        </svg>
    </div>

    <!-- Main Content -->
    <div class="container position-relative z-2" >
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center pt-5">
                <div class="animate-on-scroll visible">
                    <span class="d-inline-block py-1 px-3 rounded-pill bg-white/10 border border-white/20 text-white mb-4 backdrop-blur-sm" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px);">
                        <span class="text-primary me-2">●</span> Nouveauté : Chatbot IA Assistant
                    </span>
                </div>
                
                <h1 class="display-2 fw-bold text-white mb-4 animate-on-scroll visible delay-100" style="font-weight: 800; letter-spacing: -1px;">
                    Le stage qui change <br>
                    <span class="text-transparent bg-clip-text" style="background: linear-gradient(to right, #60a5fa, #a855f7, #ec4899); -webkit-background-clip: text; color: transparent;">votre avenir</span>
                </h1>
                
                <p class="lead text-muted mb-5 mx-auto animate-on-scroll visible delay-200" style="max-width: 600px; font-size: 1.25rem;">
                    Rejoignez la plateforme officielle du CESI. Connectez-vous avec les entreprises leaders et donnez un coup d'accélérateur à votre carrière professionnelle.
                </p>
                
                <div class="d-flex justify-content-center gap-3 animate-on-scroll visible delay-300">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="<?= BASE_URL ?>/offres" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold hover-scale shadow-lg shadow-primary/50">
                            Trouver un stage <i class="fas fa-arrow-right ms-2 opacity-75"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/register" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold hover-scale shadow-lg shadow-primary/50">
                            Créer mon profil <i class="fas fa-magic ms-2 opacity-75"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/login" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill hover-scale d-none d-md-inline-block">
                            Se connecter
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Trusted By Logos (Mockup) -->
                <div class="mt-5 pt-4 border-top border-white border-opacity-10 animate-on-scroll visible delay-500">
                    <p class="text-uppercase text-muted small fw-bold tracking-widest mb-3">Ils recrutent nos talents</p>
                    <div class="d-flex justify-content-center align-items-center gap-5 opacity-50 grayscale hover-grayscale-0 transition-all">
                        <i class="fab fa-google fa-2x"></i>
                        <i class="fab fa-microsoft fa-2x"></i>
                        <i class="fab fa-amazon fa-2x"></i>
                        <i class="fab fa-spotify fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section (Updated) -->
<div class="py-5 position-relative z-2" style="background: hsl(var(--background));">
    <div class="container py-5">
        
        <!-- Section Header -->
        <div class="text-center mb-5 pb-3 animate-on-scroll">
            <span class="d-inline-block py-1 px-3 rounded-pill bg-white/10 border border-white/20 text-white mb-3 backdrop-blur-sm" style="background: rgba(255,255,255,0.05);">
                <span class="text-primary me-2">●</span> Avantages
            </span>
            <h2 class="display-4 fw-bold text-white mb-3">L'écosystème <span class="text-transparent bg-clip-text" style="background: linear-gradient(to right, #6366f1, #06b6d4); -webkit-background-clip: text; color: transparent;">CesiStages</span></h2>
            <p class="lead text-muted mx-auto" style="max-width: 600px;">
                Tous les outils nécessaires pour que votre stage soit une réussite, de la recherche à l'évaluation finale.
            </p>
        </div>

        <div class="row g-4">
            <!-- Feature 1: Offres & Entreprises (Big Card) -->
            <div class="col-lg-6 animate-on-scroll">
                <div id="feature-offers" class="card glass-card h-100 p-0 border-0 overflow-hidden shadow-2xl group hover-lift position-relative">
                    <div class="p-5 pb-0 position-relative z-1">
                        <div class="mb-4 bg-primary bg-opacity-20 d-inline-flex p-3 rounded-circle text-primary border border-primary border-opacity-20 shadow-primary-sm">
                            <i class="fas fa-briefcase fa-2x"></i>
                        </div>
                        <h3 class="h2 text-white fw-bold mb-3">Offres Exclusives</h3>
                        <p class="text-muted mb-4" style="font-size: 1.1rem;">
                            Accédez à un réseau d'entreprises partenaires du CESI. Des offres ciblées pour votre cursus (A2, A3, Mastère).
                        </p>
                        <ul class="list-unstyled text-muted mb-0">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Stages pré-validés par les pilotes</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Candidature simplifiée en 1 clic</li>
                        </ul>
                    </div>
                    
                    <!-- Visual Bottom -->
                    <div class="mt-4 px-4 pb-4 position-relative">
                        <!-- 3 Cards Side-by-Side -->
                        <div class="d-flex flex-wrap flex-md-nowrap gap-3">
                            
                            <!-- Card 1: Capgemini -->
                            <div class="flex-fill p-3 rounded-4 border border-white border-opacity-10 shadow-lg transform-gpu group-hover:translate-y-[-8px] transition-transform duration-500" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(5px);">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary shadow-sm">
                                        <i class="fas fa-code"></i>
                                    </div>
                                    <div>
                                        <div class="h6 text-white mb-0 small fw-bold">Capgemini</div>
                                        <div class="text-xs text-muted" style="font-size: 0.7rem;">Paris</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 justify-content-center">
                                    <span class="badge bg-white bg-opacity-5 border border-white border-opacity-10 text-muted w-100 py-2 rounded-pill font-monospace" style="font-size: 0.7rem;">DevOps</span>
                                </div>
                            </div>

                            <!-- Card 2: Ubisoft -->
                            <div class="flex-fill p-3 rounded-4 border border-white border-opacity-10 shadow-lg transform-gpu group-hover:translate-y-[-12px] transition-transform duration-500" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(5px); transition-delay: 50ms;">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="bg-purple bg-opacity-10 p-2 rounded-3 text-purple shadow-sm" style="color: #a855f7;">
                                        <i class="fas fa-gamepad"></i>
                                    </div>
                                    <div>
                                        <div class="h6 text-white mb-0 small fw-bold">Ubisoft</div>
                                        <div class="text-xs text-muted" style="font-size: 0.7rem;">Montreuil</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 justify-content-center">
                                    <span class="badge bg-white bg-opacity-5 border border-white border-opacity-10 text-muted w-100 py-2 rounded-pill font-monospace" style="font-size: 0.7rem;">Unity 3D</span>
                                </div>
                            </div>

                            <!-- Card 3: Thales -->
                            <div class="flex-fill p-3 rounded-4 border border-white border-opacity-10 shadow-lg transform-gpu group-hover:translate-y-[-8px] transition-transform duration-500" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(5px); transition-delay: 100ms;">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger shadow-sm">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <div class="h6 text-white mb-0 small fw-bold">Thales</div>
                                        <div class="text-xs text-muted" style="font-size: 0.7rem;">Nantes</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 justify-content-center">
                                    <span class="badge bg-white bg-opacity-5 border border-white border-opacity-10 text-muted w-100 py-2 rounded-pill font-monospace" style="font-size: 0.7rem;">Cyber</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature 2: Suivi Pédagogique (Big Card) -->
            <div class="col-lg-6 animate-on-scroll delay-100">
                <div id="feature-suivi" class="card glass-card h-100 p-0 border-0 overflow-hidden shadow-2xl group hover-lift position-relative">
                    <div class="p-5 pb-0 position-relative z-1">
                        <div class="mb-4 bg-info bg-opacity-20 d-inline-flex p-3 rounded-circle text-info border border-info border-opacity-20 shadow-info-sm">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                        <h3 class="h2 text-white fw-bold mb-3">Suivi Pédagogique</h3>
                        <p class="text-muted mb-4" style="font-size: 1.1rem;">
                            Un lien direct avec votre pilote de promotion pour valider vos choix et suivre votre avancement.
                        </p>
                         <ul class="list-unstyled text-muted mb-0">
                            <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Validation des fiches de poste</li>
                            <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Évaluation des compétences</li>
                        </ul>
                    </div>
                    
                    <!-- Visual Bottom -->
                    <div class="mt-5 px-4 pb-4 position-relative">
                        <div class="p-4 rounded-4 border border-white border-opacity-10 shadow-lg transform-gpu group-hover:-translate-y-2 transition-transform duration-500" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(5px);">
                             <!-- Mockup UI -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:40px;height:40px;">JD</div>
                                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-dark rounded-circle"></span>
                                    </div>
                                    <div>
                                        <div class="small text-white fw-medium">M. Dupont (Pilote)</div>
                                        <div class="text-xs text-muted">En ligne</div>
                                    </div>
                                </div>
                                <div class="bg-success bg-opacity-25 text-success rounded-pill px-3 py-1 small border border-success border-opacity-25">
                                    Stage validé
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-white small p-2 rounded bg-white bg-opacity-5 border border-white border-opacity-5">
                                <i class="fas fa-bell text-warning"></i> 
                                <span class="text-muted">Nouvelle évaluation disponible</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Feature 3: Small Card -->
             <div class="col-md-4 animate-on-scroll delay-200">
                <div class="card glass-card h-100 p-4 border-0 hover-lift">
                    <div class="card-body">
                         <div class="mb-3 text-warning">
                            <i class="fas fa-file-signature fa-2x"></i>
                        </div>
                        <h4 class="text-white fw-bold">Conventions</h4>
                        <p class="text-muted small mb-0">Numérisation complète de vos conventions de stage. Signatures électroniques sécurisées.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 4: Small Card -->
             <div class="col-md-4 animate-on-scroll delay-300">
                <div class="card glass-card h-100 p-4 border-0 hover-lift">
                    <div class="card-body">
                         <div class="mb-3 text-success">
                            <i class="fas fa-history fa-2x"></i>
                        </div>
                        <h4 class="text-white fw-bold">Historique</h4>
                        <p class="text-muted small mb-0">Retrouvez toutes vos anciennes candidatures et l'historique de vos échanges avec les recruteurs.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 5: Small Card -->
             <div class="col-md-4 animate-on-scroll delay-400">
                <div class="card glass-card h-100 p-4 border-0 hover-lift">
                    <div class="card-body">
                         <div class="mb-3 text-purple">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                        <h4 class="text-white fw-bold">Chatbot IA</h4>
                        <p class="text-muted small mb-0">Une question sur votre convention ou une offre ? Notre assistant IA est là pour vous 24/7.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
// Simple Scroll Animation Script
document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Feature Offers Hover Effect
    const createHoverEffect = (cardId, items) => {
        const card = document.getElementById(cardId);
        if (!card) return;

        // Create floaters container if not exists
        let floatersContainer = document.querySelector('.floaters-container');
        if (!floatersContainer) {
            floatersContainer = document.createElement('div');
            floatersContainer.className = 'floaters-container';
            document.body.appendChild(floatersContainer);
        }

        // Create elements
        const floaters = items.map((item, index) => {
            const el = document.createElement('div');
            el.className = 'floating-offer';
            el.style.top = '0';
            el.style.left = '0';
            el.style.willChange = 'transform, opacity'; // Optimization hint for browser
            
            // Use transform for movement (Hardware Accelerated)
            // Reduced latency for tighter, smoother control
            // Staggered duration creates the trail effect without lag
            const duration = 0.15 + (index * 0.08); 
            el.style.transition = `opacity 0.2s ease, transform ${duration}s cubic-bezier(0.1, 0.5, 0.2, 1)`;
            
            el.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-10 p-2 rounded">
                        <i class="fas ${item.icon} ${item.color}"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white small">${item.title}</div>
                        <div class="text-muted" style="font-size: 0.75rem">${item.subtitle}</div>
                    </div>
                </div>
            `;
            floatersContainer.appendChild(el);
            return el;
        });

        // Mouse Move Handler
        card.addEventListener('mousemove', (e) => {
            // Use requestAnimationFrame to sync with screen refresh rate
            requestAnimationFrame(() => {
                floaters.forEach((el, index) => {
                    el.classList.add('active');
                    
                    // Positions relative to cursor
                    // Using translate3d forces GPU acceleration
                    const x = e.clientX + 20 + (index * 5); 
                    const y = e.clientY - 40 + (index * 50);

                    el.style.transform = `translate3d(${x}px, ${y}px, 0)`;
                });
            });
        });

         // Mouse Leave Handler
        card.addEventListener('mouseleave', () => {
             floaters.forEach(el => {
                el.classList.remove('active');
            });
        });
    };

    // Initialize Hover Effects
    createHoverEffect('feature-offers', [
        { title: "Dev Fullstack", subtitle: "Thales", icon: "fa-code", color: "text-info" },
        { title: "Data Analyst", subtitle: "Airbus", icon: "fa-chart-line", color: "text-warning" },
        { title: "Cyber Sec", subtitle: "Orange", icon: "fa-shield-alt", color: "text-danger" }
    ]);

    createHoverEffect('feature-suivi', [
        { title: "Livret de stage", subtitle: "Validé à 100%", icon: "fa-book", color: "text-success" },
        { title: "Rendez-vous", subtitle: "Mardi 14h00", icon: "fa-calendar-alt", color: "text-info" },
        { title: "Validation", subtitle: "Fiche de poste", icon: "fa-check-circle", color: "text-primary" }
    ]);
});
</script>

