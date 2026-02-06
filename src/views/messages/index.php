<div class="container py-5" style="margin-top: 80px; max-width: 1200px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-bold">
            <i class="fas fa-envelope me-2 text-primary"></i>Messagerie
            <?php if ($nbNonLus > 0): ?>
                <span class="badge bg-danger ms-2"><?= $nbNonLus ?> non lu<?= $nbNonLus > 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </h1>
        <a href="<?= BASE_URL ?>/messages/nouveau" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau message
        </a>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Onglets -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $type === 'recus' ? 'active' : '' ?>" href="<?= BASE_URL ?>/messages">
                <i class="fas fa-inbox me-1"></i>Reçus
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $type === 'envoyes' ? 'active' : '' ?>" href="<?= BASE_URL ?>/messages/envoyes">
                <i class="fas fa-paper-plane me-1"></i>Envoyés
            </a>
        </li>
    </ul>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <?php if (empty($messages)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                    <p class="text-muted fs-5">
                        <?php if ($type === 'recus'): ?>
                            Vous n'avez pas encore reçu de messages.
                        <?php else: ?>
                            Vous n'avez pas encore envoyé de messages.
                        <?php endif; ?>
                    </p>
                    <a href="<?= BASE_URL ?>/messages/nouveau" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Envoyer un message
                    </a>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($messages as $m): ?>
                        <?php 
                        $isNonLu = ($type === 'recus' && !$m['lu']);
                        $roleColors = [
                            'admin' => '#dc2626',
                            'pilote' => '#0ea5e9',
                            'recruteur' => '#8b5cf6',
                            'etudiant' => '#10b981'
                        ];
                        if ($type === 'recus') {
                            $personNom = $m['expediteur_prenom'] . ' ' . $m['expediteur_nom'];
                            $personRole = $m['expediteur_role'];
                        } else {
                            $personNom = $m['destinataire_prenom'] . ' ' . $m['destinataire_nom'];
                            $personRole = $m['destinataire_role'];
                        }
                        $roleColor = $roleColors[$personRole] ?? '#6b7280';
                        ?>
                        <a href="<?= BASE_URL ?>/messages/show/<?= $m['id'] ?>" 
                           class="list-group-item list-group-item-action p-3 <?= $isNonLu ? 'bg-light' : '' ?>">
                            <div class="d-flex align-items-start">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($personNom) ?>&background=<?= substr($roleColor, 1) ?>&color=fff&size=50" 
                                     class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="fw-bold <?= $isNonLu ? 'text-primary' : '' ?>"><?= htmlspecialchars($personNom) ?></span>
                                            <span class="badge ms-2" style="background-color: <?= $roleColor ?>;">
                                                <?= ucfirst($personRole) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($m['created_at'])) ?>
                                        </small>
                                    </div>
                                    <p class="mb-1 <?= $isNonLu ? 'fw-bold' : '' ?>">
                                        <?php if ($isNonLu): ?>
                                            <i class="fas fa-circle text-primary me-1" style="font-size: 8px;"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($m['sujet']) ?>
                                    </p>
                                    <p class="mb-0 text-muted small text-truncate" style="max-width: 600px;">
                                        <?= htmlspecialchars(substr($m['contenu'], 0, 100)) ?><?= strlen($m['contenu']) > 100 ? '...' : '' ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
