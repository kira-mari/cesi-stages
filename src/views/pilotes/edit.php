<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h2>Modifier le pilote</h2>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/pilotes/update/<?= $pilote['id'] ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nom">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($pilote['nom']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="prenom">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($pilote['prenom']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($pilote['email']) ?>" required>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= BASE_URL ?>/pilotes" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>