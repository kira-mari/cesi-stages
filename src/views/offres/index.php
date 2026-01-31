<div class="page-section">
    <div class="container">
        
        <div class="page-header">
            <h1>Offres de Stage</h1>
            <p>Découvrez les dernières opportunités et trouvez le stage qui vous correspond.</p>
        </div>

        <div class="filters-form">
            <form action="<?= BASE_URL ?>/offres" method="GET">
                <div class="filters-row">
                    <div class="filter-group">
                        <label class="form-label" for="search">Rechercher</label>
                        <div class="input-group">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" name="search" placeholder="Titre, description, entreprise..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <label class="form-label" for="competence">Compétence</label>
                        <div class="input-group">
                            <i class="fas fa-code"></i>
                            <select id="competence" name="competence">
                                <option value="">Toutes les compétences</option>
                                <?php if (!empty($competences)): ?>
                                    <?php foreach ($competences as $comp): ?>
                                        <option value="<?= htmlspecialchars($comp) ?>" <?= (isset($competence) && $competence === $comp) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($comp) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group" style="max-width: 150px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 3rem;">Filtrer</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pilote'])): ?>
            <div class="d-flex justify-content-end mb-4">
                <a href="<?= BASE_URL ?>/offres/create" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Créer une offre
                </a>
            </div>
        <?php endif; ?>

        <div class="offers-grid">
            <?php if (empty($offres)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h3>Aucune offre trouvée</h3>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                </div>
            <?php else: ?>
                <?php foreach ($offres as $offre): ?>
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
                                <span><i class="fas fa-coins"></i> <?= htmlspecialchars(number_format($offre['remuneration'] ?? 0, 0, ',', ' ')) ?> €/h</span>
                                <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($offre['created_at'] ?? 'now')) ?></span>
                            </div>
                        </div>

                        <div class="offer-footer">
                            <a href="<?= BASE_URL ?>/offres/<?= $offre['id'] ?>" class="btn btn-outline">Voir l'offre</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination-container mt-4 text-center">
            <div class="btn-group">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&competence=<?= urlencode($competence ?? '') ?>" 
                       class="btn btn-sm <?= ($page == $i) ? 'btn-primary' : 'btn-outline' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
