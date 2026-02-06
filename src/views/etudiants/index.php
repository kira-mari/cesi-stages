<div class="page-section">
    <div class="container">
        
        <div class="page-header">
            <h1>Gestion des Étudiants</h1>
            <p>Consultez et gérez la liste des étudiants de la promotion.</p>
        </div>

        <!-- Filters & Actions -->
        <div class="mb-5">
            <!-- Search Bar Centered -->
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

        <!-- Add Button Right -->
        <div class="d-flex justify-content-end mb-4">
            <a href="<?= BASE_URL ?>/etudiants/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Ajouter un étudiant
            </a>
        </div>

        <?php if (empty($etudiants)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-user-graduate fa-3x text-muted opacity-50"></i>
                </div>
                <h3 class="h4 text-muted">Aucun étudiant trouvé</h3>
                <p class="text-muted">Essayez de modifier votre recherche ou ajoutez un nouvel étudiant.</p>
            </div>
        <?php else: ?>
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

            <!-- Pagination -->
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
