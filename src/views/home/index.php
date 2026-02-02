
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
                        <a href="<?= BASE_URL ?>/login" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill hover-scale">
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

<!-- Features Vertical Layout -->
<div class="py-5 position-relative z-2">
    <div class="container py-5">
        
        <!-- Section Header -->
        <div class="text-center mb-5 pb-3 animate-on-scroll">
            <span class="d-inline-block py-1 px-3 rounded-pill bg-white/10 border border-white/20 text-white mb-3 backdrop-blur-sm" style="background: rgba(255,255,255,0.05);">
                <span class="text-primary me-2">●</span> Fonctionnalités
            </span>
            <h2 class="display-4 fw-bold text-white mb-3">Une plateforme <span class="text-transparent bg-clip-text" style="background: linear-gradient(to right, #6366f1, #06b6d4); -webkit-background-clip: text; color: transparent;">tout-en-un</span></h2>
            <p class="lead text-muted mx-auto" style="max-width: 600px;">
                CesiStages regroupe tous les outils dont vous avez besoin pour trouver votre stage et lancer votre carrière.
            </p>
        </div>

        <div class="row g-4">
            <!-- Feature 1: Intelligent Search (Big Card) -->
            <div class="col-lg-6 animate-on-scroll">
                <div class="card glass-card h-100 p-0 border-0 overflow-hidden shadow-2xl group hover-lift">
                    <div class="p-5 pb-0 position-relative z-1">
                        <div class="mb-4 bg-primary bg-opacity-20 d-inline-flex p-3 rounded-circle text-primary border border-primary border-opacity-20">
                            <i class="fas fa-brain fa-2x"></i>
                        </div>
                        <h3 class="h2 text-white fw-bold mb-3">Recherche IA</h3>
                        <p class="text-muted mb-4" style="font-size: 1.1rem;">
                            Notre algorithme prédictif analyse votre profil pour vous connecter avec les entreprises qui recherchent vos talents.
                        </p>
                        <ul class="list-unstyled text-muted mb-0">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Matching par compétences</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Alertes temps réel</li>
                        </ul>
                    </div>
                    
                    <!-- Visual Bottom -->
                    <div class="mt-4 ms-5 position-relative">
                        <div class="p-4 rounded-top-4 bg-dark border border-white border-opacity-10 shadow-lg transform-gpu translate-y-4 group-hover:translate-y-2 transition-transform duration-500" style="background: rgba(20, 20, 30, 0.9);">
                            <!-- Mockup UI -->
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="bg-gradient-to-br from-primary to-purple p-2 rounded-3">
                                    <i class="fas fa-code text-white"></i>
                                </div>
                                <div>
                                    <div class="h6 text-white mb-0">Développeur Fullstack</div>
                                    <div class="small text-muted">98% de compatibilité</div>
                                </div>
                                <span class="badge bg-success ms-auto">Match</span>
                            </div>
                            <!-- Tags -->
                            <div class="d-flex gap-2">
                                <span class="badge bg-dark border border-secondary text-secondary">PHP 8</span>
                                <span class="badge bg-dark border border-secondary text-secondary">Symfony</span>
                                <span class="badge bg-dark border border-secondary text-secondary">MySQL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature 2: Dashboard (Big Card) -->
            <div class="col-lg-6 animate-on-scroll delay-100">
                <div class="card glass-card h-100 p-0 border-0 overflow-hidden shadow-2xl group hover-lift">
                    <div class="p-5 pb-0 position-relative z-1">
                        <div class="mb-4 bg-info bg-opacity-20 d-inline-flex p-3 rounded-circle text-info border border-info border-opacity-20">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                        <h3 class="h2 text-white fw-bold mb-3">Suivi Centralisé</h3>
                        <p class="text-muted mb-4" style="font-size: 1.1rem;">
                            Gardez le contrôle sur votre avenir. Tableaux de bord, statistiques et suivi des candidatures en un seul endroit.
                        </p>
                         <ul class="list-unstyled text-muted mb-0">
                            <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Statut des candidatures</li>
                            <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Gestion des entretiens</li>
                        </ul>
                    </div>
                    
                    <!-- Visual Bottom -->
                    <div class="mt-4 me-5 position-relative">
                        <div class="p-4 rounded-top-end-4 bg-dark border border-white border-opacity-10 shadow-lg transform-gpu translate-y-4 group-hover:translate-y-2 transition-transform duration-500 float-end w-100" style="background: rgba(20, 20, 30, 0.9); border-top-left-radius: 1.5rem;">
                             <!-- Mockup UI -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="p-3 rounded-3 bg-white bg-opacity-5 text-center">
                                        <div class="h3 text-white mb-0">12</div>
                                        <div class="small text-muted uppercase">Envoyées</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3 bg-info bg-opacity-10 text-center">
                                        <div class="h3 text-info mb-0">3</div>
                                        <div class="small text-info uppercase">Entretiens</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-white small">
                                <i class="fas fa-sync fa-spin text-muted"></i> Synchronisation...
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
                            <i class="fas fa-bolt fa-2x"></i>
                        </div>
                        <h4 class="text-white fw-bold">Rapidité</h4>
                        <p class="text-muted small mb-0">Postulez en un clic grâce à votre profil unifié. Plus besoin de remplir 50 formulaires.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 4: Small Card -->
             <div class="col-md-4 animate-on-scroll delay-300">
                <div class="card glass-card h-100 p-4 border-0 hover-lift">
                    <div class="card-body">
                         <div class="mb-3 text-success">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <h4 class="text-white fw-bold">Fiabilité</h4>
                        <p class="text-muted small mb-0">Toutes les entreprises sont vérifiées par le CESI pour garantir des stages de qualité.</p>
                    </div>
                </div>
            </div>

            <!-- Feature 5: Small Card -->
             <div class="col-md-4 animate-on-scroll delay-400">
                <div class="card glass-card h-100 p-4 border-0 hover-lift">
                    <div class="card-body">
                         <div class="mb-3 text-purple">
                            <i class="fas fa-mobile-alt fa-2x"></i>
                        </div>
                        <h4 class="text-white fw-bold">Mobile First</h4>
                        <p class="text-muted small mb-0">Accédez à vos offres et répondez aux recruteurs directement depuis votre smartphone.</p>
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
});
</script>

