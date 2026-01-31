<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Créer une offre de stage</h2>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/offres/store" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="form-group mb-3">
                        <label for="entreprise_id">Entreprise *</label>
                        <select class="form-control" id="entreprise_id" name="entreprise_id" required>
                            <option value="">Choisir une entreprise...</option>
                            <?php foreach ($entreprises as $entreprise): ?>
                                <option value="<?= $entreprise['id'] ?>"><?= htmlspecialchars($entreprise['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="titre">Titre de l'offre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="remuneration">Rémunération (€/mois)</label>
                                <input type="number" step="0.01" class="form-control" id="remuneration" name="remuneration">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="duree">Durée (semaines)</label>
                                <input type="number" class="form-control" id="duree" name="duree">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="date_debut">Date de début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="date_fin">Date de fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Compétences requises</label>
                        <div id="competences-container">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="competences[]" placeholder="Ex: PHP, Java, Gestion de projet...">
                                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">X</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCompetence()">Ajouter une compétence</button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/offres" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Publier l'offre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function addCompetence() {
    const container = document.getElementById('competences-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="competences[]" placeholder="Compétence">
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">X</button>
    `;
    container.appendChild(div);
}
</script>