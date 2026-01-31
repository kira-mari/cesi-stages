<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h2>Créer un compte étudiant</h2>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/etudiants/store" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nom">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="prenom">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Mot de passe *</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                        <small class="form-text text-muted">Au moins 8 caractères.</small>
                    </div>

                    <!-- Ici on pourrait ajouter la gestion de la promotion -->

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= BASE_URL ?>/etudiants" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer le compte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>