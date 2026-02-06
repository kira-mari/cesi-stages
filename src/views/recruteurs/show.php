<div class="container py-5" style="margin-top: 80px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/recruteurs">Recruteurs</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($recruteur['prenom'] . ' ' . $recruteur['nom']) ?></li>
        </ol>
    </nav>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Profil du recruteur -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($recruteur['prenom'] . '+' . $recruteur['nom']) ?>&background=6366f1&color=fff&size=100" 
                         class="rounded-circle mb-3" width="100" height="100" alt="Avatar">
                    <h4 class="fw-bold"><?= htmlspecialchars($recruteur['prenom'] . ' ' . $recruteur['nom']) ?></h4>
                    <p class="text-muted mb-1"><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($recruteur['email']) ?></p>
                    <?php if ($recruteur['telephone']): ?>
                        <p class="text-muted"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($recruteur['telephone']) ?></p>
                    <?php endif; ?>
                    <span class="badge bg-primary">Recruteur</span>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">Inscrit le <?= date('d/m/Y', strtotime($recruteur['created_at'])) ?></small>
                </div>
            </div>
        </div>

        <!-- Entreprises assignées -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>Entreprises assignées</h5>
                    <span class="badge bg-primary"><?= count($entreprises) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($entreprises)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune entreprise assignée.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($entreprises as $entreprise): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($entreprise['nom']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($entreprise['secteur'] ?? 'Non défini') ?></small>
                                    </div>
                                    <a href="<?= BASE_URL ?>/recruteurs/remove-entreprise/<?= $recruteur['id'] ?>/<?= $entreprise['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Retirer cette entreprise du recruteur ?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Assigner une nouvelle entreprise -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Assigner une entreprise</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/recruteurs/assign-entreprise/<?= $recruteur['id'] ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="row g-3">
                            <div class="col-md-9">
                                <select name="entreprise_id" class="form-select" required>
                                    <option value="">-- Sélectionner une entreprise --</option>
                                    <?php 
                                    $entreprisesIds = array_column($entreprises, 'id');
                                    foreach ($toutesEntreprises as $e): 
                                        if (!in_array($e['id'], $entreprisesIds)):
                                    ?>
                                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom']) ?> (<?= $e['secteur'] ?? 'N/A' ?>)</option>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-plus me-1"></i>Assigner
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
