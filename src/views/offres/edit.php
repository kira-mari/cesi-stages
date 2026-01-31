<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Modifier l'offre</h2>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/offres/update/<?= $offre['id'] ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="form-group mb-3">
                        <label for="entreprise_id">Entreprise *</label>
                        <select class="form-control" id="entreprise_id" name="entreprise_id" required>
                            <option value="">Choisir une entreprise...</option>
                            <?php foreach ($entreprises as $entreprise): ?>
                                <option value="<?= $entreprise['id'] ?>" <?= $entreprise['id'] == $offre['entreprise_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($entreprise['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="titre">Titre de l'offre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($offre['titre']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="remuneration">Rémunération (€/mois)</label>
                                <input type="number" step="0.01" class="form-control" id="remuneration" name="remuneration" value="<?= $offre['remuneration'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="duree">Durée (semaines)</label>
                                <input type="number" class="form-control" id="duree" name="duree" value="<?= $offre['duree'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="date_debut">Date de début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?= $offre['date_debut'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="date_fin">Date de fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?= $offre['date_fin'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($offre['description']) ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Compétences requises</label>
                        <div id="competences-container">
                            <?php 
                            $comps = isset($offre['competences']) && is_array($offre['competences']) ? $offre['competences'] : [];
                            foreach ($comps as $comp): 
                            ?>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="competences[]" value="<?= htmlspecialchars($comp) ?>">
                                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">X</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCompetence()">Ajouter une compétence</button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/offres/show/<?= $offre['id'] ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
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