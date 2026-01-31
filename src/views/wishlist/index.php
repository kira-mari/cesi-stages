

<div class="page-section">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold mb-3">Ma Liste de Souhaits</h1>
            <p class="text-muted lead">Gérez vos offres favorites pour postuler plus tard.</p>
            <div class="mt-3">
                <span class="badge bg-primary rounded-pill px-4 py-2 text-uppercase tracking-wider">
                    <?= count($offres ?? []) ?> Offre<?= count($offres ?? []) > 1 ? 's' : '' ?> Sauvegardée<?= count($offres ?? []) > 1 ? 's' : '' ?>
                </span>
            </div>
        </div>

        <?php if (empty($offres)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-dark rounded-circle border border-secondary shadow-lg" style="width: 120px; height: 120px;">
                        <i class="far fa-heart fa-4x text-muted opacity-50"></i>
                    </div>
                </div>
                <h3 class="h2 text-white mb-3">Votre liste est vide</h3>
                <p class="text-muted mb-5 fs-5">Vous n'avez pas encore coup de cœur ? Explorez les offres disponibles.</p>
                <a href="<?= BASE_URL ?>/offres" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg transition-transform hover-scale">
                    <i class="fas fa-search me-2"></i> Explorer les offres
                </a>
            </div>
        <?php else: ?>
            <div class="offers-grid">
                <?php foreach ($offres as $offre): ?>
                    <article class="offer-card position-relative d-flex flex-column h-100" style="position: relative;">
                        <!-- Remove Button -->
                        <div style="position: absolute; top: 15px; right: 15px; z-index: 10;">
                            <a href="<?= BASE_URL ?>/offres/removeFromWishlist/<?= $offre['id'] ?>" 
                               class="btn btn-icon bg-dark border-0 shadow text-danger hover-danger rounded-circle" 
                               style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                               title="Retirer de ma liste">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>

                        <div class="offer-header mb-3">
                            <div>
                                <h3 class="mb-2" style="padding-right: 3rem;">
                                    <a href="<?= BASE_URL ?>/offres/show/<?= $offre['id'] ?>" class="text-white text-decoration-none stretched-link">
                                        <?= htmlspecialchars($offre['titre']) ?>
                                    </a>
                                </h3>
                                <div class="offer-company d-flex align-items-center text-primary">
                                    <i class="fas fa-building me-2"></i> 
                                    <span class="fw-bold"><?= htmlspecialchars($offre['entreprise_nom']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="offer-body flex-grow-1 d-flex flex-column">
                            <p class="text-muted mb-4 flex-grow-1">
                                <?= htmlspecialchars(substr($offre['description'], 0, 150)) . (strlen($offre['description']) > 150 ? '...' : '') ?>
                            </p>
                            
                            <div class="offer-tags d-flex flex-wrap gap-2 mb-4">
                                <span class="badge bg-dark border border-secondary text-secondary rounded-pill px-3 py-2">
                                    <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($offre['adresse'] ?? 'Non précisé') ?>
                                </span>
                                <span class="badge bg-dark border border-secondary text-secondary rounded-pill px-3 py-2">
                                    <i class="far fa-clock me-2"></i><?= htmlspecialchars($offre['duree']) ?> sem.
                                </span>
                                <span class="badge bg-dark border border-secondary text-secondary rounded-pill px-3 py-2">
                                    <i class="fas fa-euro-sign me-2"></i><?= htmlspecialchars($offre['remuneration']) ?>/mois
                                </span>
                            </div>

                            <div class="mt-auto pt-3 border-top border-secondary">
                                <div class="d-grid">
                                    <a href="<?= BASE_URL ?>/offres/show/<?= $offre['id'] ?>" class="btn btn-outline-primary rounded-pill">
                                        Voir l'offre
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

