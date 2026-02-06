<section class="dashboard-section">
    <div class="container">
        <div class="dashboard-header">
            <h1>Tableau de bord</h1>
            <p>Bienvenue, <?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?> !</p>
        </div>

        <?php if ($_SESSION['user_role'] === 'recruteur' && isset($stats['nb_entreprises']) && $stats['nb_entreprises'] == 0): ?>
            <div class="alert alert-warning alert-dismissible fade show mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <strong>Configuration requise</strong>
                        <p class="mb-0">Vous n'êtes pas encore associé à une entreprise. Pour publier des offres et recevoir des candidatures, veuillez configurer votre entreprise.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/recruteur/configurer-entreprise" class="btn btn-warning ms-auto">
                        <i class="fas fa-building me-2"></i>Configurer maintenant
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-number"><?= $stats['total_offres'] ?></span>
                    <span class="stat-label">Offres disponibles</span>
                </div>
            </div>
            
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-number"><?= $stats['total_entreprises'] ?></span>
                    <span class="stat-label">Entreprises</span>
                </div>
            </div>
            
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['total_etudiants'] ?></span>
                        <span class="stat-label">Étudiants</span>
                    </div>
                </div>
                
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['total_pilotes'] ?></span>
                        <span class="stat-label">Pilotes</span>
                    </div>
                </div>
            <?php elseif ($_SESSION['user_role'] === 'pilote'): ?>
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['candidatures_etudiants'] ?></span>
                        <span class="stat-label">Candidatures de mes étudiants</span>
                    </div>
                </div>
            <?php elseif ($_SESSION['user_role'] === 'etudiant'): ?>
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['mes_candidatures'] ?></span>
                        <span class="stat-label">Mes candidatures</span>
                    </div>
                </div>
                
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['wishlist_count'] ?></span>
                        <span class="stat-label">Offres en wishlist</span>
                    </div>
                </div>
            <?php elseif ($_SESSION['user_role'] === 'recruteur'): ?>
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['total_candidatures'] ?></span>
                        <span class="stat-label">Candidatures reçues</span>
                    </div>
                </div>
                
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['nb_entreprises'] ?? 0 ?></span>
                        <span class="stat-label">Entreprises assignées</span>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Statistiques Messagerie (pour tous les rôles) -->
            <?php if (isset($stats['messages'])): ?>
                <div class="stat-card" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number">
                            <?= $stats['messages']['non_lus'] ?>
                            <?php if ($stats['messages']['non_lus'] > 0): ?>
                                <span class="badge bg-danger ms-1" style="font-size: 0.5em; vertical-align: middle;">nouveau</span>
                            <?php endif; ?>
                        </span>
                        <span class="stat-label">Messages non lus</span>
                        <small style="opacity: 0.8; font-size: 0.75em;">
                            <?= $stats['messages']['recus'] ?> reçus · <?= $stats['messages']['envoyes'] ?> envoyés
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Actions rapides -->
        <div class="quick-actions">
            <h2>Actions rapides</h2>
            <div class="actions-grid">
                <a href="<?= BASE_URL ?>/offres" class="action-card">
                    <i class="fas fa-search"></i>
                    <span>Rechercher une offre</span>
                </a>
                <a href="<?= BASE_URL ?>/entreprises" class="action-card">
                    <i class="fas fa-building"></i>
                    <span>Voir les entreprises</span>
                </a>
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'pilote'): ?>
                    <a href="<?= BASE_URL ?>/entreprises/create" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Ajouter une entreprise</span>
                    </a>
                    <a href="<?= BASE_URL ?>/offres/create" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Ajouter une offre</span>
                    </a>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="<?= BASE_URL ?>/etudiants/create" class="action-card">
                        <i class="fas fa-user-plus"></i>
                        <span>Ajouter un étudiant</span>
                    </a>
                    <a href="<?= BASE_URL ?>/pilotes/create" class="action-card">
                        <i class="fas fa-user-plus"></i>
                        <span>Ajouter un pilote</span>
                    </a>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'etudiant'): ?>
                    <a href="<?= BASE_URL ?>/wishlist" class="action-card">
                        <i class="fas fa-heart"></i>
                        <span>Ma wishlist</span>
                    </a>
                    <a href="<?= BASE_URL ?>/candidatures/etudiant" class="action-card">
                        <i class="fas fa-file-alt"></i>
                        <span>Mes candidatures</span>
                    </a>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'recruteur'): ?>
                    <a href="<?= BASE_URL ?>/offres/create" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Publier une offre</span>
                    </a>
                    <a href="<?= BASE_URL ?>/recruteur/candidatures" class="action-card">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Voir les candidatures</span>
                    </a>
                    <a href="<?= BASE_URL ?>/recruteur/mes-entreprises" class="action-card">
                        <i class="fas fa-building"></i>
                        <span>Mes entreprises</span>
                    </a>
                <?php endif; ?>
                
                <a href="<?= BASE_URL ?>/messages" class="action-card">
                    <i class="fas fa-envelope"></i>
                    <span>Messagerie</span>
                    <?php if (isset($stats['messages']) && $stats['messages']['non_lus'] > 0): ?>
                        <span class="badge bg-danger position-absolute" style="top: 10px; right: 10px;"><?= $stats['messages']['non_lus'] ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="<?= BASE_URL ?>/dashboard/stats" class="action-card">
                    <i class="fas fa-chart-bar"></i>
                    <span>Statistiques</span>
                </a>
            </div>
        </div>
        
        <!-- Dernières offres -->
        <div class="recent-offers">
            <div class="section-header">
                <h2>Dernières offres</h2>
                <a href="<?= BASE_URL ?>/offres" class="btn btn-outline btn-sm">Voir tout</a>
            </div>
            
            <div class="offers-grid">
                <?php foreach ($dernieresOffres as $offre): ?>
                    <div class="offer-card">
                        <div class="offer-header">
                            <h3><?= htmlspecialchars($offre['titre']) ?></h3>
                            <span class="offer-company"><?= htmlspecialchars($offre['entreprise_nom'] ?? 'Entreprise') ?></span>
                        </div>
                        
                        <div class="offer-body">
                            <div class="offer-skills">
                                <?php 
                                    if (!empty($offre['competences'])) {
                                        $creationParams = json_decode($offre['competences'], true);
                                        if (is_array($creationParams)) {
                                            foreach (array_slice($creationParams, 0, 3) as $comp) {
                                                echo '<span class="skill-tag">' . htmlspecialchars($comp) . '</span>';
                                            }
                                            if (count($creationParams) > 3) {
                                                echo '<span class="skill-tag">+' . (count($creationParams) - 3) . '</span>';
                                            }
                                        }
                                    }
                                ?>
                            </div>
                            
                            <p>
                                <?= nl2br(htmlspecialchars(substr($offre['description'], 0, 150) . '...')) ?>
                            </p>

                            <div class="offer-meta">
                                <span><i class="far fa-clock"></i> <?= htmlspecialchars($offre['duree'] ?? 'N/C') ?> mois</span>
                                <span><i class="fas fa-coins"></i> <?= htmlspecialchars(number_format($offre['remuneration'] ?? 0, 0, ',', ' ')) ?> € / mois</span>
                                <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($offre['created_at'] ?? 'now')) ?></span>
                            </div>
                        </div>

                        <div class="offer-footer">
                            <a href="<?= BASE_URL ?>/offres/<?= $offre['id'] ?>" class="btn btn-outline btn-block w-100">Voir l'offre</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
