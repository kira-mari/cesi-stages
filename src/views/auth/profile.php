<div class="container py-5">
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

                            <div class="mt-5 text-end">
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

<style>
/* Petits ajustements inline pour forcer les bordures si Bootstrap interfère */
.border-end { border-right: 1px solid hsl(var(--border)) !important; }
.hover-danger:hover { background-color: hsl(var(--destructive)) !important; color: white !important; border-color: hsl(var(--destructive)) !important; }
</style>
