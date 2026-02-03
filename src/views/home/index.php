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
</style>

<div class="hero position-relative overflow-hidden d-flex align-items-center" style="min-height: 90vh;">
    <!-- Animated Background Layer -->
    <div class="hero-animated-bg">
        <div class="hero-blob"></div>
        <div class="hero-blob"></div>
        <div class="hero-blob"></div>
        <!-- Grid Pattern Overlay -->
        <div style="position: absolute; inset:0; background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px); background-size: 50px 50px; opacity: 0.2; mask-image: radial-gradient(circle at center, black 0%, transparent 80%); -webkit-mask-image: radial-gradient(circle at center, black 0%, transparent 80%);"></div>
    </div>

    <!-- Main Content -->
    <div class="container position-relative z-2">
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
<div class="py-5 position-relative z-2">
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
                    <div class="mt-5 px-4 pb-4 position-relative">
                        <div class="p-4 rounded-4 border border-white border-opacity-10 shadow-lg transform-gpu group-hover:-translate-y-2 transition-transform duration-500" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(5px);">
                            <!-- Mockup UI -->
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="bg-gradient-to-br from-primary to-purple p-2 rounded-3 text-white shadow-sm">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div class="h6 text-white mb-0">Capgemini</div>
                                    <div class="small text-muted">Partenaire Gold</div>
                                </div>
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 ms-auto rounded-pill px-3">Vérifié</span>
                            </div>
                            <!-- Tags -->
                            <div class="d-flex gap-2">
                                <span class="badge bg-white bg-opacity-5 border border-white border-opacity-10 text-muted px-3 py-2 rounded-pill font-monospace">DevOps</span>
                                <span class="badge bg-white bg-opacity-5 border border-white border-opacity-10 text-muted px-3 py-2 rounded-pill font-monospace">Cloud</span>
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
            // Add dynamic transition for "trail" effect
            // Each card follows with a slightly longer delay
            el.style.transition = `opacity 0.3s ease, transform 0.3s ease, left ${0.2 + (index * 0.15)}s cubic-bezier(0.2, 0.8, 0.2, 1), top ${0.2 + (index * 0.15)}s cubic-bezier(0.2, 0.8, 0.2, 1)`;
            
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
             floaters.forEach((el, index) => {
                el.classList.add('active');
                
                // Calculate position with offsets
                // Stagger positions around cursor
                const angle = (index / floaters.length) * Math.PI * 2;
                const radius = 100; // Distance from cursor
                
                const offsetX = Math.cos(angle) * (radius + (index * 10));
                const offsetY = Math.sin(angle) * (radius + (index * 10));

                const x = e.clientX + 20 + (index * 10);
                const y = e.clientY - 50 + (index * 60);

                el.style.left = `${x}px`;
                el.style.top = `${y}px`;
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

