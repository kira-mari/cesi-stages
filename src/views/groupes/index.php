<div class="page-section" style="margin-top: 80px;">
    <div class="container">
        <div class="page-header">
            <h1>Mes groupes</h1>
            <p>Organisez vos étudiants assignés en groupes.</p>
        </div>


        <?php if (empty($groupes)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-users fa-3x text-muted opacity-50"></i>
                </div>
                <h3 class="h4 text-muted">Aucun groupe</h3>
                <p class="text-muted">Créez votre premier groupe pour organiser vos étudiants.</p>
                <a href="<?= BASE_URL ?>/groupes/create" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Créer un groupe
                </a>
            </div>
        <?php else: ?>
            <div class="users-grid">
                <?php foreach ($groupes as $groupe): ?>
                    <div class="user-card">
                        <div class="user-avatar">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="user-info">
                            <h3><?= htmlspecialchars($groupe['nom']) ?></h3>
                            <span class="user-email">
                                <i class="fas fa-user-graduate me-1"></i>
                                <?= (int) ($groupe['nb_etudiants'] ?? 0) ?> étudiant(s)
                            </span>
                        </div>
                        <div class="user-card-actions">
                            <a href="<?= BASE_URL ?>/groupes/show/<?= $groupe['id'] ?>" class="btn btn-action-primary">
                                <i class="fas fa-eye me-2"></i>Gérer
                            </a>
                            <a href="<?= BASE_URL ?>/groupes/edit/<?= $groupe['id'] ?>" class="btn btn-action-icon btn-action-warning" title="Renommer">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/groupes/delete/<?= $groupe['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce groupe ? Les étudiants ne seront pas désassignés.');">
                                <button type="submit" class="btn btn-action-icon btn-action-danger" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
