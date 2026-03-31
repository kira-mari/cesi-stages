<div class="page-section">
    <div class="container">
        
        <div class="page-header">
            <h1>Gestion des Étudiants</h1>
            <p>Consultez et gérez la liste des étudiants de la promotion.</p>
        </div>

        <!-- Filters & Actions -->
        <div class="mb-5">
            <div class="w-100">
                <form action="<?= BASE_URL ?>/etudiants" method="GET" class="w-100">
                    <div class="search-bar-modern">
                        <div class="search-bar-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" name="search" class="search-bar-input" placeholder="Nom, Prénom, Email..." value="<?= htmlspecialchars($search ?? '') ?>">
                        <button type="submit" class="btn btn-primary search-bar-btn">
                            <span>Rechercher</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <?php if (!empty($isPilote) && (!empty($groupes) || !empty($sansGroupe))): ?>
            <!-- ===== Vue groupes drag & drop pour pilotes ===== -->
            <div class="groupes-board" id="groupes-board">
                <!-- Colonne "Sans groupe" -->
                <div class="groupe-column" data-groupe-id="0">
                    <div class="groupe-column-header sans-groupe">
                        <h5><i class="fas fa-inbox me-2"></i>Sans groupe</h5>
                        <span class="badge bg-secondary"><?= count($sansGroupe) ?></span>
                    </div>
                    <div class="groupe-column-body dropzone" data-groupe-id="0">
                        <?php foreach ($sansGroupe as $etu): ?>
                            <div class="etudiant-drag-card" draggable="true" data-etudiant-id="<?= $etu['id'] ?>">
                                <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                                <div class="etudiant-drag-info">
                                    <strong><?= htmlspecialchars($etu['nom'] . ' ' . $etu['prenom']) ?></strong>
                                    <small><?= htmlspecialchars($etu['email']) ?></small>
                                </div>
                                <a href="<?= BASE_URL ?>/etudiants/show/<?= $etu['id'] ?>" class="btn btn-sm btn-link" title="Voir profil">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($sansGroupe)): ?>
                            <div class="dropzone-empty">Glissez des étudiants ici pour les retirer de leur groupe</div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php foreach ($groupes as $groupe): ?>
                    <div class="groupe-column" data-groupe-id="<?= $groupe['id'] ?>">
                        <div class="groupe-column-header">
                            <h5><i class="fas fa-users me-2"></i><?= htmlspecialchars($groupe['nom']) ?></h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary"><?= count($groupe['etudiants']) ?></span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/groupes/edit/<?= $groupe['id'] ?>"><i class="fas fa-pen me-2"></i>Renommer</a></li>
                                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/groupes/delete/<?= $groupe['id'] ?>" onclick="return confirm('Supprimer ce groupe ?')"><i class="fas fa-trash me-2"></i>Supprimer</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="groupe-column-body dropzone" data-groupe-id="<?= $groupe['id'] ?>">
                            <?php foreach ($groupe['etudiants'] as $etu): ?>
                                <div class="etudiant-drag-card" draggable="true" data-etudiant-id="<?= $etu['id'] ?>">
                                    <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                                    <div class="etudiant-drag-info">
                                        <strong><?= htmlspecialchars($etu['nom'] . ' ' . $etu['prenom']) ?></strong>
                                        <small><?= htmlspecialchars($etu['email']) ?></small>
                                    </div>
                                    <a href="<?= BASE_URL ?>/etudiants/show/<?= $etu['id'] ?>" class="btn btn-sm btn-link" title="Voir profil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($groupe['etudiants'])): ?>
                                <div class="dropzone-empty">Glissez des étudiants ici</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($etudiants) && empty($isPilote)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-user-graduate fa-3x text-muted opacity-50"></i>
                </div>
                <h3 class="h4 text-muted">Aucun étudiant trouvé</h3>
                <p class="text-muted">Essayez de modifier votre recherche ou ajoutez un nouvel étudiant.</p>
            </div>
        <?php elseif (!empty($isPilote) && empty($groupes) && empty($sansGroupe) && empty($etudiants)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-user-graduate fa-3x text-muted opacity-50"></i>
                </div>
                <h3 class="h4 text-muted">Aucun étudiant trouvé</h3>
                <p class="text-muted">Aucun étudiant ne vous est assigné pour le moment.</p>
            </div>
        <?php endif; ?>

        <!-- Vue liste classique (admins ou recherche) -->
        <?php if (!empty($etudiants) && (empty($isPilote) || !empty($search))): ?>
            <?php if (!empty($isPilote) && !empty($search)): ?>
                <h4 class="mt-4 mb-3"><i class="fas fa-search me-2"></i>Résultats de recherche</h4>
            <?php endif; ?>
            <div class="users-grid">
                <?php foreach ($etudiants as $etudiant): ?>
                    <div class="user-card">
                        <div class="user-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        
                        <div class="user-info">
                            <h3><?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></h3>
                            <span class="user-email" title="<?= htmlspecialchars($etudiant['email']) ?>">
                                <i class="far fa-envelope me-1"></i> <?= htmlspecialchars($etudiant['email']) ?>
                            </span>
                        </div>

                        <div class="user-card-actions">
                            <a href="<?= BASE_URL ?>/etudiants/show/<?= $etudiant['id'] ?>" class="btn btn-action-primary" title="Voir le profil">
                                <i class="fas fa-eye me-2"></i> Profil
                            </a>
                            <a href="<?= BASE_URL ?>/messages/nouveau?destinataire=<?= $etudiant['id'] ?>" class="btn btn-action-icon btn-action-info" title="Contacter" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/etudiants/edit/<?= $etudiant['id'] ?>" class="btn btn-action-icon btn-action-warning" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/etudiants/delete/<?= $etudiant['id'] ?>" method="POST" class="d-inline h-100 mb-0" style="display:contents" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">
                                <button type="submit" class="btn btn-action-icon btn-action-danger w-100" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="pagination-container mt-5 text-center">
                    <div class="btn-group">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" 
                               class="btn btn-sm <?= ($page == $i) ? 'btn-primary' : 'btn-outline' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($isPilote)): ?>
<script>
(function() {
    const BASE = '<?= BASE_URL ?>';
    let draggedCard = null;
    let sourceZone = null;

    document.querySelectorAll('.etudiant-drag-card').forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedCard = this;
            sourceZone = this.closest('.dropzone');
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.etudiantId);
        });

        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            document.querySelectorAll('.dropzone').forEach(z => z.classList.remove('drag-over'));
            draggedCard = null;
            sourceZone = null;
        });
    });

    document.querySelectorAll('.dropzone').forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });

        zone.addEventListener('dragleave', function(e) {
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
        });

        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            if (!draggedCard) return;

            const targetGroupeId = parseInt(this.dataset.groupeId);
            const sourceGroupeId = parseInt(sourceZone.dataset.groupeId);
            const etudiantId = parseInt(draggedCard.dataset.etudiantId);

            if (targetGroupeId === sourceGroupeId) return;

            // Remove "empty" placeholder
            const emptyMsg = this.querySelector('.dropzone-empty');
            if (emptyMsg) emptyMsg.remove();

            this.appendChild(draggedCard);

            // Re-add placeholder to source if now empty
            if (!sourceZone.querySelector('.etudiant-drag-card')) {
                const placeholder = document.createElement('div');
                placeholder.className = 'dropzone-empty';
                placeholder.textContent = sourceGroupeId === 0
                    ? 'Glissez des étudiants ici pour les retirer de leur groupe'
                    : 'Glissez des étudiants ici';
                sourceZone.appendChild(placeholder);
            }

            // Update badge counts
            updateBadgeCounts();

            fetch(BASE + '/groupes/move-etudiant', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ etudiant_id: etudiantId, groupe_id: targetGroupeId })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    location.reload();
                }
            })
            .catch(() => location.reload());
        });
    });

    function updateBadgeCounts() {
        document.querySelectorAll('.groupe-column').forEach(col => {
            const count = col.querySelectorAll('.etudiant-drag-card').length;
            const badge = col.querySelector('.badge');
            if (badge) badge.textContent = count;
        });
    }
})();
</script>
<?php endif; ?>
