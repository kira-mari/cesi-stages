<div class="page-section">
    <div class="container">
        
        <div class="page-header">
            <h1>Entreprises Partenaires</h1>
            <p>Découvrez les entreprises qui recrutent et leurs opportunités de stage.</p>
        </div>

        <div class="filters-form">
            <form action="<?= BASE_URL ?>/entreprises" method="GET">
                <div class="filters-row">
                    <div class="filter-group">
                        <label class="form-label" for="search">Rechercher</label>
                        <div class="input-group">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" name="search" placeholder="Nom de l'entreprise..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <label class="form-label" for="ville">Ville</label>
                        <div class="input-group">
                            <i class="fas fa-map-marker-alt"></i>
                            <select id="ville" name="ville">
                                <option value="">Toutes les villes</option>
                                <?php if (!empty($villes)): ?>
                                    <?php foreach ($villes as $v): ?>
                                        <option value="<?= htmlspecialchars($v) ?>" <?= (isset($ville) && $ville === $v) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($v) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group" style="max-width: 150px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 3rem; margin-top: 1.8rem;">Filtrer</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pilote'])): ?>
            <div class="d-flex justify-content-end mb-4">
                <a href="<?= BASE_URL ?>/entreprises/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Ajouter une entreprise
                </a>
            </div>
        <?php endif; ?>

        <div class="companies-grid">
            <?php if (empty($entreprises)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-building fa-3x mb-3 text-muted"></i>
                    <h3>Aucune entreprise trouvée</h3>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                </div>
            <?php else: ?>
                <?php foreach ($entreprises as $entreprise): ?>
                    <div class="company-card">
                        <div class="company-header-row">
                            <div class="company-logo-placeholder">
                                <span><?= strtoupper(substr($entreprise['nom'], 0, 1)) ?></span>
                            </div>
                            <div class="company-info-right">
                                <h3><?= htmlspecialchars($entreprise['nom'] ?? 'Entreprise') ?></h3>
                                <?php if (!empty($entreprise['secteur'])): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($entreprise['secteur']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="company-body">
                            <p class="description">
                                <?= empty($entreprise['description']) ? 'Aucune description disponible.' : htmlspecialchars(substr($entreprise['description'], 0, 150) . (strlen($entreprise['description']) > 150 ? '...' : '')) ?>
                            </p>

                            <div class="company-meta">
                                <?php if (!empty($entreprise['adresse'])): ?>
                                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($entreprise['adresse']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($entreprise['email'])): ?>
                                    <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($entreprise['email']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="company-footer">
                            <a href="<?= BASE_URL ?>/entreprises/show/<?= $entreprise['id'] ?>" class="btn btn-outline w-100">Voir la fiche</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination-container mt-4 text-center">
            <div class="btn-group">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" 
                       class="btn btn-sm <?= ($page == $i) ? 'btn-primary' : 'btn-outline' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
