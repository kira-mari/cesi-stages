<div class="container py-5" style="margin-top: 80px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-bold">
            <i class="fas fa-clipboard-list me-2 text-primary"></i>Candidatures reçues
        </h1>
    </div>

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

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="display-5 fw-bold text-primary"><?= $stats['en_attente'] + $stats['acceptee'] + $stats['refusee'] ?></h3>
                    <p class="mb-0 text-muted">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="display-5 fw-bold text-warning"><?= $stats['en_attente'] ?></h3>
                    <p class="mb-0 text-muted">En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="display-5 fw-bold text-success"><?= $stats['acceptee'] ?></h3>
                    <p class="mb-0 text-muted">Acceptées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="display-5 fw-bold text-danger"><?= $stats['refusee'] ?></h3>
                    <p class="mb-0 text-muted">Refusées</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= BASE_URL ?>/recruteur/candidatures" 
                   class="btn <?= $filtreStatut === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Toutes
                </a>
                <a href="<?= BASE_URL ?>/recruteur/candidatures?statut=en_attente" 
                   class="btn <?= $filtreStatut === 'en_attente' ? 'btn-warning' : 'btn-outline-warning' ?>">
                    <i class="fas fa-clock me-1"></i>En attente (<?= $stats['en_attente'] ?>)
                </a>
                <a href="<?= BASE_URL ?>/recruteur/candidatures?statut=acceptee" 
                   class="btn <?= $filtreStatut === 'acceptee' ? 'btn-success' : 'btn-outline-success' ?>">
                    <i class="fas fa-check me-1"></i>Acceptées (<?= $stats['acceptee'] ?>)
                </a>
                <a href="<?= BASE_URL ?>/recruteur/candidatures?statut=refusee" 
                   class="btn <?= $filtreStatut === 'refusee' ? 'btn-danger' : 'btn-outline-danger' ?>">
                    <i class="fas fa-times me-1"></i>Refusées (<?= $stats['refusee'] ?>)
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des candidatures -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($candidatures)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">
                        <?php if ($filtreStatut !== 'all'): ?>
                            Aucune candidature avec ce statut.
                        <?php else: ?>
                            Vous n'avez pas encore reçu de candidatures.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Candidat</th>
                                <th>Offre</th>
                                <th>Entreprise</th>
                                <th>Documents</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidatures as $c): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($c['etudiant_prenom'] . '+' . $c['etudiant_nom']) ?>&background=random&color=fff" 
                                                 class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                                            <div>
                                                <strong><?= htmlspecialchars($c['etudiant_prenom'] . ' ' . $c['etudiant_nom']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($c['etudiant_email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($c['offre_titre']) ?></td>
                                    <td><?= htmlspecialchars($c['entreprise_nom']) ?></td>
                                    <td>
                                        <span class="me-2" title="<?= !empty($c['cv_path']) ? 'CV fourni' : 'Pas de CV' ?>">
                                            <i class="fas fa-file-pdf <?= !empty($c['cv_path']) ? 'text-danger' : 'text-muted' ?>"></i>
                                        </span>
                                        <span title="<?= !empty($c['lettre_motivation']) ? 'Lettre fournie' : 'Pas de lettre' ?>">
                                            <i class="fas fa-file-alt <?= !empty($c['lettre_motivation']) ? 'text-info' : 'text-muted' ?>"></i>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'en_attente' => 'bg-warning text-dark',
                                            'acceptee' => 'bg-success',
                                            'refusee' => 'bg-danger'
                                        ][$c['statut']] ?? 'bg-secondary';
                                        $statutLabel = [
                                            'en_attente' => 'En attente',
                                            'acceptee' => 'Acceptée',
                                            'refusee' => 'Refusée'
                                        ][$c['statut']] ?? $c['statut'];
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>/recruteur/candidature/<?= $c['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($c['statut'] === 'en_attente'): ?>
                                                <form action="<?= BASE_URL ?>/recruteur/candidature/update/<?= $c['id'] ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                    <input type="hidden" name="redirect" value="list">
                                                    <button type="submit" name="statut" value="acceptee" class="btn btn-sm btn-success" title="Accepter">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="<?= BASE_URL ?>/recruteur/candidature/update/<?= $c['id'] ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                    <input type="hidden" name="redirect" value="list">
                                                    <button type="submit" name="statut" value="refusee" class="btn btn-sm btn-danger" title="Refuser">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
