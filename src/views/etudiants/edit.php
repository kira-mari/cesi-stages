<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h2>Modifier l'étudiant</h2>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/etudiants/update/<?= $etudiant['id'] ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nom">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($etudiant['nom']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="prenom">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($etudiant['email']) ?>" required>
                    </div>

                    <?php if (!empty($pilotes)): ?>
                    <div class="form-group mb-3">
                        <label for="pilote_id">Pilote assigné (Admin)</label>
                        <select class="form-control" id="pilote_id" name="pilote_id">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($pilotes as $pilote): ?>
                                <option value="<?= $pilote['id'] ?>" 
                                    <?= ($currentPilote && $currentPilote['id'] == $pilote['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pilote['nom'] . ' ' . $pilote['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Sélectionnez le pilote en charge de cet étudiant.</small>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= BASE_URL ?>/etudiants" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>