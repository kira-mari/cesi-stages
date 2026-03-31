<div class="page-section" style="margin-top: 80px;">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h1><?= htmlspecialchars($groupe['nom']) ?></h1>
                <p>Gérer les membres du groupe.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/groupes/edit/<?= $groupe['id'] ?>" class="btn btn-outline-primary">
                    <i class="fas fa-pen me-2"></i>Renommer
                </a>
                <a href="<?= BASE_URL ?>/groupes" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Étudiants dans le groupe -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Membres du groupe (<?= count($etudiants) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($etudiants)): ?>
                            <p class="text-muted mb-0">Aucun étudiant dans ce groupe.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($etudiants as $e): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <strong><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></strong>
                                            <br><small class="text-muted"><?= htmlspecialchars($e['email']) ?></small>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <a href="<?= BASE_URL ?>/etudiants/show/<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary" title="Profil">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="<?= BASE_URL ?>/groupes/remove/<?= $groupe['id'] ?>/<?= $e['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Retirer cet étudiant du groupe ?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer du groupe">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Étudiants disponibles -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Ajouter un étudiant</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($disponibles)): ?>
                            <p class="text-muted mb-0">Tous vos étudiants sont déjà dans un groupe ou vous n'avez aucun étudiant assigné.</p>
                            <a href="<?= BASE_URL ?>/etudiants" class="btn btn-outline-primary mt-2">Voir mes étudiants</a>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($disponibles as $e): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <strong><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></strong>
                                            <br><small class="text-muted"><?= htmlspecialchars($e['email']) ?></small>
                                        </div>
                                        <form action="<?= BASE_URL ?>/groupes/add-etudiant/<?= $groupe['id'] ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="etudiant_id" value="<?= $e['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus me-1"></i>Ajouter
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
