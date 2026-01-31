<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Modifier l'entreprise</h2>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/entreprises/update/<?= $entreprise['id'] ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="form-group mb-3">
                        <label for="nom">Nom de l'entreprise *</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($entreprise['nom']) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="secteur">Secteur d'activité</label>
                        <input type="text" class="form-control" id="secteur" name="secteur" value="<?= htmlspecialchars($entreprise['secteur']) ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($entreprise['description']) ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email de contact *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($entreprise['email']) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="telephone">Téléphone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($entreprise['telephone']) ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label for="adresse">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($entreprise['adresse']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/entreprises/show/<?= $entreprise['id'] ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>