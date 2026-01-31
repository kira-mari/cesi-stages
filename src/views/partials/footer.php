<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-grid">
                <!-- Brand Section -->
                <div class="footer-brand">
                    <a href="<?= BASE_URL ?>" class="footer-logo">
                        <div class="logo-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <span class="logo-text"><?= APP_NAME ?></span>
                    </a>
                    <p class="footer-desc">
                        Connecter les talents de demain avec les entreprises d''aujourd''hui.
                        La plateforme officielle des stages CESI.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="footer-nav-group">
                    <h4 class="footer-heading">Plateforme</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/offres">Rechercher une offre</a></li>
                        <li><a href="<?= BASE_URL ?>/entreprises">Entreprises partenaires</a></li>
                        <li><a href="<?= BASE_URL ?>/dashboard">Espace étudiant</a></li>
                        <li><a href="<?= BASE_URL ?>/login">Connexion</a></li>
                    </ul>
                </div>
                
                <div class="footer-nav-group">
                    <h4 class="footer-heading">Ressources</h4>
                    <ul class="footer-links">
                        <li><a href="https://www.cesi.fr" target="_blank">Site du CESI</a></li>
                        <li><a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a></li>
                        <li><a href="<?= BASE_URL ?>/contact">Support</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="footer-contact">
                    <h4 class="footer-heading">Nous contacter</h4>
                    <ul class="contact-list">
                        <li>
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <span>Campus CESI</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope text-primary"></i>
                            <a href="mailto:contact@cesi-stages.fr">contact@cesi-stages.fr</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p class="copyright">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Tous droits réservés.</p>
                <p class="credits">Fait avec <i class="fas fa-heart text-danger"></i> pour les étudiants</p>
            </div>
        </div>
    </div>
</footer>
