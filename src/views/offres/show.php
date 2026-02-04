<div class="page-section">
    <div class="container">
        
        <div class="mb-4">
            <a href="<?= BASE_URL ?>/offres" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left"></i> Retour aux offres
            </a>
        </div>

        <div class="two-column-layout">
            <!-- Main Content -->
            <div class="main-column">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h1 class="h2 mb-2"><?= htmlspecialchars($offre['titre']) ?></h1>
                                <a href="<?= BASE_URL ?>/entreprises/<?= $offre['entreprise_id'] ?>" class="text-primary font-weight-bold h5 text-decoration-none">
                                    <i class="fas fa-building mr-2"></i> <?= htmlspecialchars($offre['entreprise_nom']) ?>
                                </a>
                            </div>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'etudiant'): ?>
                                <?php if ($inWishlist): ?>
                                    <a href="<?= BASE_URL ?>/offres/removeFromWishlist/<?= $offre['id'] ?>" class="btn btn-icon btn-warning" title="Retirer de la wishlist">
                                        <i class="fas fa-star"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/offres/addToWishlist/<?= $offre['id'] ?>" class="btn btn-icon btn-outline" title="Ajouter à la wishlist">
                                        <i class="far fa-star"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="grid-2-cols mb-4 py-3 border-top border-bottom">
                            <div>
                                <p class="mb-2 text-muted"><i class="fas fa-map-marker-alt width-20"></i> Lieu</p>
                                <p class="font-weight-bold"><?= htmlspecialchars($offre['entreprise_adresse'] ?? 'Non précisé') ?></p>
                            </div>
                            <div>
                                <p class="mb-2 text-muted"><i class="far fa-clock width-20"></i> Durée</p>
                                <p class="font-weight-bold"><?= $offre['duree'] ?> mois</p>
                            </div>
                            <div>
                                <p class="mb-2 text-muted"><i class="fas fa-coins width-20"></i> Rémunération</p>
                                <p class="font-weight-bold"><?= number_format($offre['remuneration'] ?? 0, 0, ',', ' ') ?> € / mois</p>
                            </div>
                            <div>
                                <p class="mb-2 text-muted"><i class="far fa-calendar-alt width-20"></i> Dates</p>
                                <p class="font-weight-bold">
                                    <?php if($offre['date_debut']): ?>
                                        <?= date('d/m/Y', strtotime($offre['date_debut'])) ?>
                                    <?php else: ?>
                                        Dès que possible
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h5 mb-3">Description du poste</h3>
                            <div class="text-muted line-height-relaxed">
                                <?= nl2br(htmlspecialchars($offre['description'])) ?>
                            </div>
                        </div>

                        <div>
                            <h3 class="h5 mb-3">Compétences requises</h3>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $comp = json_decode($offre['competences'], true);
                                if ($comp && is_array($comp)): 
                                    foreach($comp as $c): ?>
                                        <span class="skill-tag"><?= htmlspecialchars($c) ?></span>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-column">
                <?php if (isset($_SESSION['user_role'])): ?>
                    <?php if ($_SESSION['user_role'] === 'etudiant'): ?>
                        <div class="card mb-4 sticky-top">
                            <div class="card-header">
                                <h3 class="h5 m-0">Postuler</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($aPostule): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle mr-2"></i> Vous avez déjà postulé.
                                    </div>
                                <?php else: ?>
                                    <form action="<?= BASE_URL ?>/offres/postuler/<?= $offre['id'] ?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        
                                        <div class="form-group mb-3">
                                            <label class="form-label">Lettre de motivation</label>
                                            <textarea name="lettre_motivation" class="input w-100" rows="6" placeholder="Présentez-vous en quelques lignes..." required></textarea>
                                        </div>
                                        
                                        <div class="form-group mb-4">
                                            <label class="form-label">CV (PDF, DOC)</label>
                                            <input type="file" name="cv" class="input w-100" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100">Envoyer ma candidature</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif (in_array($_SESSION['user_role'], ['admin', 'pilote'])): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="h5 m-0">Administration</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Candidatures</span>
                                    <span class="badge badge-primary rounded-pill"><?= $nbCandidatures ?></span>
                                </div>
                                <hr class="border-secondary my-3">
                                <div class="d-grid gap-2">
                                    <a href="<?= BASE_URL ?>/offres/edit/<?= $offre['id'] ?>" class="btn btn-outline w-100">Modifier</a>
                                    <a href="<?= BASE_URL ?>/offres/delete/<?= $offre['id'] ?>" class="btn btn-danger w-100" onclick="return confirm(''Êtes-vous sûr de vouloir supprimer cette offre ?'');">Supprimer</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                     <div class="card mb-4">
                        <div class="card-body text-center">
                            <h3 class="h5 mb-3">Intéressé ?</h3>
                            <p class="text-muted mb-4">Connectez-vous pour postuler à cette offre.</p>
                            <a href="<?= BASE_URL ?>/login" class="btn btn-primary w-100">Se connecter</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

