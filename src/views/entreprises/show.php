<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h1 class="card-title"><?= htmlspecialchars($entreprise['nom']) ?></h1>
                <p class="text-muted"><?= htmlspecialchars($entreprise['secteur']) ?></p>
                <hr>
                <h5>Description</h5>
                <p class="card-text"><?= nl2br(htmlspecialchars($entreprise['description'])) ?></p>
                
                <div class="mt-4">
                    <h5>Coordonnées</h5>
                    <p>
                        <strong>Email :</strong> <?= htmlspecialchars($entreprise['email']) ?><br>
                        <strong>Téléphone :</strong> <?= htmlspecialchars($entreprise['telephone']) ?><br>
                        <strong>Adresse :</strong> <?= htmlspecialchars($entreprise['adresse']) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Offres de stage</h3>
            </div>
            <div class="card-body">
                <?php if (empty($offres)): ?>
                    <p class="text-muted">Aucune offre disponible pour le moment.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($offres as $offre): ?>
                            <a href="<?= BASE_URL ?>/offres/show/<?= $offre['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?= htmlspecialchars($offre['titre']) ?></h5>
                                    <small><?= date('d/m/Y', strtotime($offre['created_at'])) ?></small>
                                </div>
                                <p class="mb-1"><?= substr(htmlspecialchars($offre['description']), 0, 100) ?>...</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Évaluations</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h2 class="display-4"><?= number_format($moyenneEvaluations, 1) ?>/5</h2>
                    <div class="ratings">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <span class="fa fa-star <?= $i <= round($moyenneEvaluations) ? 'checked' : '' ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>

                <?php if (empty($evaluations)): ?>
                    <p class="text-muted text-center">Aucune évaluation.</p>
                <?php else: ?>
                    <div class="evaluations-list">
                        <?php foreach ($evaluations as $eval): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>Note : <?= $eval['note'] ?>/5</strong>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($eval['created_at'])) ?></small>
                                </div>
                                <p class="mb-0"><?= htmlspecialchars($eval['commentaire']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pilote', 'etudiant'])): ?>
                    <hr>
                    <h5>Donner votre avis</h5>
                    <form action="<?= BASE_URL ?>/entreprises/evaluate/<?= $entreprise['id'] ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <div class="form-group mb-2">
                            <label>Note</label>
                            <select name="note" class="form-control" required>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Très bien</option>
                                <option value="3">3 - Bien</option>
                                <option value="2">2 - Moyen</option>
                                <option value="1">1 - Mauvais</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Commentaire</label>
                            <textarea name="commentaire" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Envoyer</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pilote'])): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Administration</h3>
                </div>
                <div class="card-body">
                    <a href="<?= BASE_URL ?>/entreprises/edit/<?= $entreprise['id'] ?>" class="btn btn-warning btn-block mb-2">Modifier</a>
                    <a href="<?= BASE_URL ?>/entreprises/delete/<?= $entreprise['id'] ?>" class="btn btn-danger btn-block" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ?');">Supprimer</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>