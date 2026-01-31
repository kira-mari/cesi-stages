<div class="page-section">
    <div class="container">
        
        <div class="page-header">
            <h1>Gestion des Pilotes</h1>
            <p>Consultez et administrez l'équipe pédagogique.</p>
        </div>

        <!-- Filters & Actions -->
        <div class="mb-5">
            <!-- Search Bar Centered -->
            <div class="w-100">
                <form action="<?= BASE_URL ?>/pilotes" method="GET" class="w-100">
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
            <a href="<?= BASE_URL ?>/pilotes/create" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Ajouter un pilote
            </a>
        </div>

        <?php if (empty($pilotes)): ?>
             <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-user-tie fa-3x text-muted opacity-50"></i>
                </div>
                <h3 class="h4 text-muted">Aucun pilote trouvé</h3>
                <p class="text-muted">Essayez de modifier votre recherche ou ajoutez un nouveau pilote.</p>
            </div>
        <?php else: ?>
            <div class="users-grid">
                <?php foreach ($pilotes as $pilote): ?>
                    <div class="user-card">
                        <div class="user-avatar" style="background: hsl(38 92% 50% / 0.1); color: hsl(38 92% 50%); border-color: hsl(38 92% 50% / 0.2);">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        
                        <div class="user-info">
                            <h3><?= htmlspecialchars($pilote['nom'] . ' ' . $pilote['prenom']) ?></h3>
                            <span class="user-email" title="<?= htmlspecialchars($pilote['email']) ?>">
                                <i class="far fa-envelope mr-1"></i> <?= htmlspecialchars($pilote['email']) ?>
                            </span>
                        </div>

                        <div class="user-card-actions">
                            <a href="<?= BASE_URL ?>/pilotes/show/<?= $pilote['id'] ?>" class="btn btn-action-primary" title="Voir le profil">
                                <i class="fas fa-eye mr-2"></i> Profil
                            </a>
                            <a href="<?= BASE_URL ?>/pilotes/edit/<?= $pilote['id'] ?>" class="btn btn-action-icon btn-action-warning" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/pilotes/delete/<?= $pilote['id'] ?>" method="POST" class="d-inline h-100 mb-0" style="display:contents" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce pilote ?');">
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
