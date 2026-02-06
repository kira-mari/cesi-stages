<div class="container py-5" style="margin-top: 80px; max-width: 900px;">
    <div class="text-center mb-5">
        <div class="mb-4">
            <i class="fas fa-building fa-4x text-primary"></i>
        </div>
        <h1 class="h2 fw-bold">Demander une assignation d'entreprise</h1>
        <p class="text-muted">Pour publier des offres et recevoir des candidatures, vous devez être associé à une entreprise. Envoyez une demande aux administrateurs.</p>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_info'])): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?= $_SESSION['flash_info']; unset($_SESSION['flash_info']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($entreprisesExistantes)): ?>
        <!-- Le recruteur a déjà une entreprise -->
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            Vous êtes déjà associé à <?= count($entreprisesExistantes) ?> entreprise(s).
            <a href="<?= BASE_URL ?>/dashboard" class="alert-link">Aller au tableau de bord</a>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Option 1: Sélectionner une entreprise existante -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-search me-2"></i>Demander à rejoindre une entreprise existante
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Si votre entreprise est déjà référencée sur notre plateforme, demandez à y être associé.</p>
                    
                    <form action="<?= BASE_URL ?>/recruteur/configurer-entreprise" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="action" value="select_existing">
                        
                        <div class="mb-4">
                            <label for="entreprise_id" class="form-label fw-bold">Entreprise</label>
                            <select name="entreprise_id" id="entreprise_id" class="form-select" required>
                                <option value="">Sélectionnez une entreprise...</option>
                                <?php foreach ($toutesEntreprises as $e): ?>
                                    <option value="<?= $e['id'] ?>">
                                        <?= htmlspecialchars($e['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer la demande
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Option 2: Créer une nouvelle entreprise -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-plus-circle me-2"></i>Demander la création d'une nouvelle entreprise
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Si votre entreprise n'est pas encore référencée, demandez sa création.</p>
                    
                    <form action="<?= BASE_URL ?>/recruteur/configurer-entreprise" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="action" value="create_new">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label fw-bold">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="nom" class="form-control" required placeholder="Ex: TechCorp">
                        </div>
                        
                        <div class="mb-3">
                            <label for="secteur" class="form-label">Secteur d'activité</label>
                            <input type="text" name="secteur" id="secteur" class="form-control" placeholder="Ex: Informatique, Finance...">
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" name="adresse" id="adresse" class="form-control" placeholder="Ex: 123 Rue de Paris, 75001 Paris">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="email_entreprise" class="form-label">Email</label>
                                <input type="email" name="email_entreprise" id="email_entreprise" class="form-control" placeholder="contact@entreprise.fr">
                            </div>
                            <div class="col-6">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" name="telephone" id="telephone" class="form-control" placeholder="01 23 45 67 89">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="site_web" class="form-label">Site web</label>
                            <input type="url" name="site_web" id="site_web" class="form-control" placeholder="https://www.entreprise.fr">
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Décrivez brièvement votre entreprise..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer la demande de création
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Faire plus tard
        </a>
    </div>
</div>
