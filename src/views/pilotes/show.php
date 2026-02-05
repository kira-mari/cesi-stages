<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="fas fa-user-tie fa-5x text-secondary mb-3"></i>
                <h2 class="card-title"><?= htmlspecialchars($pilote['prenom'] . ' ' . $pilote['nom']) ?></h2>
                <p class="text-muted">Pilote de promotion</p>
                <div class="mt-3">
                    <p><i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($pilote['email']) ?></p>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Étudiants gérés</h3>
            </div>
            <div class="card-body">
                <?php if (empty($etudiants)): ?>
                    <p class="text-muted">Ce pilote ne gère aucun étudiant pour le moment.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($etudiants as $etudiant): ?>
                            <a href="<?= BASE_URL ?>/etudiants/show/<?= $etudiant['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h5>
                                    <small><?= $etudiant['email'] ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="<?= BASE_URL ?>/pilotes" class="btn btn-secondary">Retour à la liste</a>
            <div>
                <a href="<?= BASE_URL ?>/pilotes/edit/<?= $pilote['id'] ?>" class="btn btn-warning">Modifier</a>
                <a href="<?= BASE_URL ?>/pilotes/delete/<?= $pilote['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce pilote ?');">Supprimer</a>
            </div>
        </div>
    </div>
</div>