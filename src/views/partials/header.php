<header class="header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <a href="<?= BASE_URL ?>" class="logo">
                <i class="fas fa-briefcase"></i>
                <span><?= APP_NAME ?></span>
            </a>
            
            <!-- Navigation Desktop -->
            <nav class="nav-desktop">
                <ul class="nav-menu">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li><a href="<?= BASE_URL ?>" class="nav-link">Accueil</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>/offres" class="nav-link">Offres</a></li>
                    <li><a href="<?= BASE_URL ?>/entreprises" class="nav-link">Entreprises</a></li>
                    
                    <?php if (isset($_SESSION['user_role'])): ?>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="<?= BASE_URL ?>/etudiants" class="nav-link">Étudiants</a></li>
                            <li><a href="<?= BASE_URL ?>/pilotes" class="nav-link">Pilotes</a></li>
                        <?php elseif ($_SESSION['user_role'] === 'pilote'): ?>
                            <li><a href="<?= BASE_URL ?>/etudiants" class="nav-link">Étudiants</a></li>
                            <li><a href="<?= BASE_URL ?>/candidatures/pilote" class="nav-link">Candidatures</a></li>
                        <?php elseif ($_SESSION['user_role'] === 'etudiant'): ?>
                            <li><a href="<?= BASE_URL ?>/wishlist" class="nav-link">Wishlist</a></li>
                            <li><a href="<?= BASE_URL ?>/candidatures/etudiant" class="nav-link">Mes candidatures</a></li>
                        <?php endif; ?>
                        
                        <li><a href="<?= BASE_URL ?>/dashboard" class="nav-link">Tableau de bord</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <!-- User Actions -->
            <div class="user-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <button class="user-menu-toggle" onclick="toggleUserMenu()">
                            <i class="fas fa-user-circle"></i>
                            <span><?= htmlspecialchars($_SESSION['user_prenom']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></span>
                                <span class="user-role"><?= htmlspecialchars(ucfirst($_SESSION['user_role'])) ?></span>
                            </div>
                            <hr>
                            <a href="<?= BASE_URL ?>/dashboard" class="dropdown-item">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                            <a href="<?= BASE_URL ?>/logout" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Burger Menu -->
            <button class="burger-menu" onclick="toggleMobileMenu()" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <nav class="nav-mobile" id="mobileMenu">
        <ul class="nav-menu-mobile">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li><a href="<?= BASE_URL ?>" class="nav-link">Accueil</a></li>
            <?php endif; ?>
            <li><a href="<?= BASE_URL ?>/offres" class="nav-link">Offres</a></li>
            <li><a href="<?= BASE_URL ?>/entreprises" class="nav-link">Entreprises</a></li>
            
            <?php if (isset($_SESSION['user_role'])): ?>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="<?= BASE_URL ?>/etudiants" class="nav-link">Étudiants</a></li>
                    <li><a href="<?= BASE_URL ?>/pilotes" class="nav-link">Pilotes</a></li>
                <?php elseif ($_SESSION['user_role'] === 'pilote'): ?>
                    <li><a href="<?= BASE_URL ?>/etudiants" class="nav-link">Étudiants</a></li>
                    <li><a href="<?= BASE_URL ?>/candidatures/pilote" class="nav-link">Candidatures</a></li>
                <?php elseif ($_SESSION['user_role'] === 'etudiant'): ?>
                    <li><a href="<?= BASE_URL ?>/wishlist" class="nav-link">Wishlist</a></li>
                    <li><a href="<?= BASE_URL ?>/candidatures/etudiant" class="nav-link">Mes candidatures</a></li>
                <?php endif; ?>
                
                <li><a href="<?= BASE_URL ?>/dashboard" class="nav-link">Tableau de bord</a></li>
                <li><a href="<?= BASE_URL ?>/logout" class="nav-link">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="<?= BASE_URL ?>/login" class="nav-link">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
