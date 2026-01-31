<div class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Trouvez votre stage de rêve aujourd'hui.</h1>
            <p class="hero-description">
                Connectez-vous avec les meilleures entreprises et lancez votre carrière. 
                Une plateforme simple et puissante pour gérer vos candidatures.
            </p>
            <div class="hero-actions">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="<?= BASE_URL ?>/offres" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i> Voir les offres
                    </a>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline btn-lg">
                        <i class="fas fa-columns"></i> Mon Tableau de bord
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <i class="fas fa-rocket"></i>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <span class="stat-number">150+</span>
                <span class="stat-label">Offres de stage</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-building"></i>
                <span class="stat-number">50+</span>
                <span class="stat-label">Entreprises Partenaires</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <span class="stat-number">1200+</span>
                <span class="stat-label">Étudiants Inscrits</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <span class="stat-number">300+</span>
                <span class="stat-label">Stages Pourvus</span>
            </div>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <h2 class="section-title">Pourquoi nous choisir ?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Simple et Rapide</h3>
                <p>Postulez aux offres en un clic. Notre interface est conçue pour vous faire gagner du temps dans vos recherches.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Offres Vérifiées</h3>
                <p>Toutes les entreprises et leurs offres sont vérifiées par nos soins pour garantir la qualité des stages proposés.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Suivi en Temps Réel</h3>
                <p>Suivez l'état de vos candidatures directement depuis votre tableau de bord personnel.</p>
            </div>
        </div>
    </div>
</div>

<div class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Prêt à commencer ?</h2>
            <p>Rejoignez des centaines d'étudiants qui ont déjà trouvé leur stage grâce à notre plateforme.</p>
            <?php if (!isset($_SESSION['user'])): ?>
                <a href="<?= BASE_URL ?>/login" class="btn btn-primary btn-lg">Créer un compte</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/offres" class="btn btn-primary btn-lg">Parcourir les offres</a>
            <?php endif; ?>
        </div>
    </div>
</div>
