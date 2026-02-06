<div class="container py-5" style="margin-top: 80px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Demandes d'approbation</h1>
            <p class="text-muted mb-0">Gérez les demandes de comptes Pilote et Recruteur</p>
        </div>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?= $stats['pending'] ?></h3>
                            <small class="opacity-75">En attente</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                            <i class="fas fa-check fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?= $stats['approved_today'] ?></h3>
                            <small class="opacity-75">Approuvés aujourd'hui</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?= $stats['total_approved'] ?></h3>
                            <small class="opacity-75">Total approuvés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des demandes -->
    <?php if (empty($requests)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5>Aucune demande en attente</h5>
                <p class="text-muted">Toutes les demandes d'approbation ont été traitées.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>Demandes en attente (<?= count($requests) ?>)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle demandé</th>
                            <th>Date de demande</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($request['prenom'] . '+' . $request['nom']) ?>&background=random&color=fff" 
                                             class="rounded-circle me-2" width="40" height="40" alt="Avatar">
                                        <div>
                                            <strong><?= htmlspecialchars($request['prenom'] . ' ' . $request['nom']) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($request['email']) ?></td>
                                <td>
                                    <?php if ($request['role'] === 'pilote'): ?>
                                        <span class="badge bg-info"><i class="fas fa-chalkboard-teacher me-1"></i>Pilote</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-briefcase me-1"></i>Recruteur</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($request['approval_requested_at']): ?>
                                        <?= date('d/m/Y à H:i', strtotime($request['approval_requested_at'])) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <form action="<?= BASE_URL ?>/approbations/approve/<?= $request['id'] ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                                <i class="fas fa-check me-1"></i>Approuver
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $request['id'] ?>" title="Refuser">
                                            <i class="fas fa-times me-1"></i>Refuser
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de refus -->
                            <div class="modal fade" id="rejectModal<?= $request['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Refuser la demande</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= BASE_URL ?>/approbations/reject/<?= $request['id'] ?>" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <p>Êtes-vous sûr de vouloir refuser la demande de <strong><?= htmlspecialchars($request['prenom'] . ' ' . $request['nom']) ?></strong> ?</p>
                                                <div class="mb-3">
                                                    <label for="reason<?= $request['id'] ?>" class="form-label">Raison du refus (optionnel)</label>
                                                    <textarea name="reason" id="reason<?= $request['id'] ?>" class="form-control" rows="3" placeholder="Expliquez pourquoi la demande est refusée..."></textarea>
                                                </div>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Le compte sera rétrogradé en compte Étudiant.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Refuser la demande</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
