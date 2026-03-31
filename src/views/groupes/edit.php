<div class="page-section" style="margin-top: 80px;">
    <div class="container">
        <div class="page-header">
            <h1>Modifier le groupe</h1>
            <p>Renommez votre groupe.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>/groupes/edit/<?= $groupe['id'] ?>" method="POST">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom du groupe</label>
                                <input type="text" name="nom" id="nom" class="form-control" required maxlength="255" value="<?= htmlspecialchars($groupe['nom']) ?>">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= BASE_URL ?>/groupes/show/<?= $groupe['id'] ?>" class="btn btn-outline-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
