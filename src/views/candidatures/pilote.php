<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-3">Candidatures des étudiants</h1>
        <hr>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($candidatures)): ?>
            <div class="alert alert-info text-center my-5">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p class="lead">Aucune candidature trouvée pour vos étudiants.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Étudiant</th>
                            <th>Offre</th>
                            <th>Entreprise</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-end">CV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatures as $candidature): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold">
                                        <?= htmlspecialchars($candidature['etudiant_prenom'] . ' ' . $candidature['etudiant_nom']) ?>
                                    </div>
                                    <small class="text-muted"><?= htmlspecialchars($candidature['etudiant_email']) ?></small>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/offres/show/<?= $candidature['offre_id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($candidature['offre_titre']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-building text-muted me-2"></i>
                                        <?= htmlspecialchars($candidature['entreprise_nom']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($candidature['created_at'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($candidature['statut']) {
                                            'acceptee' => 'success',
                                            'refusee' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php 
                                        echo match($candidature['statut']) {
                                            'acceptee' => 'Acceptée',
                                            'refusee' => 'Refusée',
                                            default => 'En attente'
                                        };
                                        ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if (!empty($candidature['cv_path'])): ?>
                                        <a href="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($candidature['cv_path']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank" 
                                           title="Télécharger le CV">
                                            <i class="fas fa-file-download"></i> CV
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Aucun CV</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>