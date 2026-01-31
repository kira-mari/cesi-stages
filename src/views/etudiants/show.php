<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-5x text-secondary mb-3"></i>
                <h2 class="card-title"><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h2>
                <p class="text-muted">Étudiant</p>
                <div class="mt-3">
                    <p><i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($etudiant['email']) ?></p>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Candidatures</h3>
            </div>
            <div class="card-body">
                <?php if (empty($candidatures)): ?>
                    <p class="text-muted">Cet étudiant n'a pas encore postulé à des offres.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($candidatures as $candidature): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">
                                        <a href="<?= BASE_URL ?>/offres/show/<?= $candidature['offre_id'] ?>">
                                            <?= htmlspecialchars($candidature['offre_titre']) ?>
                                        </a>
                                    </h5>
                                    <small><?= date('d/m/Y', strtotime($candidature['created_at'])) ?></small>
                                </div>
                                <p class="mb-1">Statut : 
                                    <span class="badge bg-<?php 
                                        echo match($candidature['statut']) {
                                            'acceptee' => 'success',
                                            'refusee' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?= htmlspecialchars($candidature['statut']) ?>
                                    </span>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="<?= BASE_URL ?>/etudiants" class="btn btn-secondary">Retour à la liste</a>
            <div>
                <a href="<?= BASE_URL ?>/etudiants/edit/<?= $etudiant['id'] ?>" class="btn btn-warning">Modifier</a>
                <a href="<?= BASE_URL ?>/etudiants/delete/<?= $etudiant['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">Supprimer</a>
            </div>
        </div>
    </div>
</div>