<div class="container py-5" style="margin-top: 80px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-bold">
            <i class="fas fa-building me-2 text-primary"></i>Mes entreprises
        </h1>
    </div>

    <?php if (empty($entreprises)): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Aucune entreprise assignée</h4>
                <p class="text-muted">Contactez l'administrateur pour vous faire assigner des entreprises.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($entreprises as $entreprise): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($entreprise['nom']) ?></h5>
                            <?php if ($entreprise['secteur']): ?>
                                <span class="badge bg-primary mb-2"><?= htmlspecialchars($entreprise['secteur']) ?></span>
                            <?php endif; ?>
                            
                            <?php if ($entreprise['description']): ?>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(substr($entreprise['description'], 0, 100)) ?>...
                                </p>
                            <?php endif; ?>
                            
                            <div class="small text-muted">
                                <?php if ($entreprise['email']): ?>
                                    <p class="mb-1"><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($entreprise['email']) ?></p>
                                <?php endif; ?>
                                <?php if ($entreprise['telephone']): ?>
                                    <p class="mb-1"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($entreprise['telephone']) ?></p>
                                <?php endif; ?>
                                <?php if ($entreprise['adresse']): ?>
                                    <p class="mb-0"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($entreprise['adresse']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="<?= BASE_URL ?>/entreprises/<?= $entreprise['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye me-1"></i>Voir les offres
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
