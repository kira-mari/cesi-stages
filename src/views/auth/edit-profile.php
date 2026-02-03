<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-white"><i class="fas fa-user-edit me-2"></i>Modifier mon profil</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/profile/update" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="row gx-5">
                            <!-- Informations de base -->
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h5 class="text-primary mb-4 pb-2" style="border-bottom: 1px solid hsl(var(--border));">
                                    <i class="fas fa-info-circle me-2"></i>Informations de base
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="nom" class="form-label text-muted small text-uppercase font-weight-bold">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required placeholder="Votre nom">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="prenom" class="form-label text-muted small text-uppercase font-weight-bold">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required placeholder="Votre prénom">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label text-muted small text-uppercase font-weight-bold">Email</label>
                                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                    <div class="form-text text-muted small mt-1"><i class="fas fa-lock me-1"></i> L'email ne peut pas être modifié.</div>
                                </div>
                                
                                 <div class="mb-3">
                                    <label for="age" class="form-label text-muted small text-uppercase font-weight-bold">Âge</label>
                                    <input type="number" class="form-control" id="age" name="age" value="<?= isset($user['age']) ? htmlspecialchars($user['age']) : '' ?>" placeholder="Votre âge">
                                </div>
                            </div>
                            
                            <!-- Coordonnées & Bio -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-4 pb-2" style="border-bottom: 1px solid hsl(var(--border));">
                                    <i class="fas fa-address-card me-2"></i>Détails supplémentaires
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="telephone" class="form-label text-muted small text-uppercase font-weight-bold">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= isset($user['telephone']) ? htmlspecialchars($user['telephone']) : '' ?>" placeholder="Votre numéro de téléphone">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="adresse" class="form-label text-muted small text-uppercase font-weight-bold">Adresse</label>
                                    <textarea class="form-control" id="adresse" name="adresse" rows="3" placeholder="Votre adresse complète"><?= isset($user['adresse']) ? htmlspecialchars($user['adresse']) : '' ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label text-muted small text-uppercase font-weight-bold">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="4" placeholder="Quelques mots à propos de vous..."><?= isset($user['bio']) ? htmlspecialchars($user['bio']) : '' ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center mt-4 pt-3" style="border-top: 1px solid hsl(var(--border));">
                            <a href="<?= BASE_URL ?>/profile" class="btn btn-outline text-muted me-3">Annuler</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ajustements pour les inputs si nécessaire */
.form-label { display: block; margin-bottom: 0.5rem; }
.form-text { font-size: 0.85em; }

/* Amélioration de la lisibilité des placeholders et champs désactivés */
.form-control::placeholder {
    color: hsl(var(--muted-foreground));
    opacity: 0.7;
}

.form-control:disabled {
    background-color: hsl(var(--input) / 0.3) !important;
    color: hsl(var(--foreground)) !important;
    opacity: 0.8;
    cursor: not-allowed;
    border-color: hsl(var(--border) / 0.5);
}
</style>
