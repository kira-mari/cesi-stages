<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <!-- Carte de Profil -->
        <div class="col-lg-10">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-foreground"><i class="fas fa-id-card me-2"></i>Mon Profil</h4>
                    <a href="<?= BASE_URL ?>/profile/edit" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($_SESSION['flash_success'])): ?>
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div>
                                <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Colonne de Gauche (Avatar & Rôle) -->
                        <div class="col-md-4 text-center border-end mb-4 mb-md-0" style="border-right: 1px solid hsl(var(--border));">
                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                 style="width: 150px; height: 150px; background-color: hsl(var(--input)); color: hsl(var(--primary)); box-shadow: 0 0 20px rgba(0,0,0,0.2);">
                                <i class="fas fa-user fa-5x"></i>
                            </div>
                            
                            <h2 class="h4 font-weight-bold mb-1"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
                            <span class="badge bg-primary px-3 py-2 text-uppercase mb-4"><?= htmlspecialchars($user['role']) ?></span>
                            
                            <?php if (!empty($user['bio'])): ?>
                                <div class="text-start p-3 rounded" style="background-color: hsl(var(--input) / 0.3);">
                                    <label class="text-muted small text-uppercase font-weight-bold mb-2 d-block">À propos</label>
                                    <p class="mb-0 small text-secondary font-italic">
                                        "<?= nl2br(htmlspecialchars($user['bio'])) ?>"
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Colonne de Droite (Détails) -->
                        <div class="col-md-8 ps-md-4" style="padding-left: 1.5rem;">
                            <h5 class="text-primary border-bottom pb-2 mb-4" style="border-color: hsl(var(--border)) !important;">Informations personnelles</h5>
                            
                            <div class="user-info-list">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-4 text-muted">Email</div>
                                    <div class="col-sm-8 font-weight-medium"><?= htmlspecialchars($user['email']) ?></div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-4 text-muted">Téléphone</div>
                                    <div class="col-sm-8">
                                        <?php if (!empty($user['telephone'])): ?>
                                            <?= htmlspecialchars($user['telephone']) ?>
                                        <?php else: ?>
                                            <span class="text-muted font-italic small">Non renseigné</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-4 text-muted">Âge</div>
                                    <div class="col-sm-8">
                                        <?php if (!empty($user['age'])): ?>
                                            <?= htmlspecialchars($user['age']) ?> ans
                                        <?php else: ?>
                                            <span class="text-muted font-italic small">Non renseigné</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-start">
                                    <div class="col-sm-4 text-muted">Adresse</div>
                                    <div class="col-sm-8">
                                        <?php if (!empty($user['adresse'])): ?>
                                            <?= nl2br(htmlspecialchars($user['adresse'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted font-italic small">Non renseigné</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-4 text-muted">Inscrit depuis</div>
                                    <div class="col-sm-8"><?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?></div>
                                </div>
                            </div>

                            <?php if ($user['role'] === 'recruteur' && !empty($entreprises)): ?>
                            <h5 class="text-primary border-bottom pb-2 mb-4 mt-5" style="border-color: hsl(var(--border)) !important;">
                                <i class="fas fa-building me-2"></i>Entreprises assignées
                            </h5>
                            
                            <div class="entreprises-list">
                                <?php foreach ($entreprises as $entreprise): ?>
                                    <div class="entreprise-item p-3 mb-3 rounded border" style="background-color: hsl(var(--input) / 0.3); border-color: hsl(var(--border)) !important;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-primary font-weight-bold">
                                                    <i class="fas fa-building me-2"></i><?= htmlspecialchars($entreprise['nom']) ?>
                                                </h6>
                                                <?php if ($entreprise['secteur']): ?>
                                                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($entreprise['secteur']) ?></span>
                                                <?php endif; ?>
                                                
                                                <div class="entreprise-details small text-muted mt-2">
                                                    <?php if ($entreprise['email']): ?>
                                                        <div class="mb-1">
                                                            <i class="fas fa-envelope me-2"></i><?= htmlspecialchars($entreprise['email']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($entreprise['telephone']): ?>
                                                        <div class="mb-1">
                                                            <i class="fas fa-phone me-2"></i><?= htmlspecialchars($entreprise['telephone']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($entreprise['adresse']): ?>
                                                        <div class="mb-1">
                                                            <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($entreprise['adresse']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <?php if ($entreprise['description']): ?>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <?= htmlspecialchars(substr($entreprise['description'], 0, 150)) ?>...
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ms-3">
                                                <a href="<?= BASE_URL ?>/entreprises/<?= $entreprise['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>Voir les offres
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <div class="mt-5 d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                    <i class="fas fa-trash-alt me-2"></i> Supprimer mon compte
                                </button>
                                <a href="<?= BASE_URL ?>/logout" class="btn btn-outline btn-sm text-danger border-danger hover-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Se déconnecter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Supprimer mon compte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="<?= BASE_URL ?>/profile/delete" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Attention !</strong> Cette action est irréversible.
                    </div>
                    
                    <p>La suppression de votre compte entraînera :</p>
                    <ul>
                        <li>La perte de toutes vos données personnelles</li>
                        <li>La suppression de vos candidatures</li>
                        <li>La suppression de vos messages</li>
                    </ul>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Confirmez avec votre mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Entrez votre mot de passe pour confirmer">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Petits ajustements inline pour forcer les bordures si Bootstrap interfère */
.border-end { border-right: 1px solid hsl(var(--border)) !important; }
.hover-danger:hover { background-color: hsl(var(--destructive)) !important; color: white !important; border-color: hsl(var(--destructive)) !important; }

/* Styles pour la section entreprises */
.entreprises-list {
    margin-top: 1rem;
}

.entreprise-item {
    transition: all 0.2s ease;
}

.entreprise-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.entreprise-details div {
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
}

.entreprise-details i {
    color: hsl(var(--primary));
    width: 1rem;
    margin-right: 0.5rem;
}
</style>
