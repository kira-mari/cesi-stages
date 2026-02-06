<div class="container py-5" style="margin-top: 80px; max-width: 900px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/messages">Messagerie</a></li>
            <li class="breadcrumb-item active">Message</li>
        </ol>
    </nav>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php 
    $isExpéditeur = ($message['expediteur_id'] == $_SESSION['user_id']);
    $roleColors = [
        'admin' => '#dc2626',
        'pilote' => '#0ea5e9',
        'recruteur' => '#8b5cf6',
        'etudiant' => '#10b981'
    ];
    ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
            <h4 class="mb-0 text-white">
                <i class="fas fa-envelope-open me-2"></i><?= htmlspecialchars($message['sujet']) ?>
            </h4>
        </div>
        <div class="card-body p-4">
            <!-- En-tête du message -->
            <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <?php 
                    $expNom = $message['expediteur_prenom'] . ' ' . $message['expediteur_nom'];
                    $expRole = $message['expediteur_role'];
                    $expColor = $roleColors[$expRole] ?? '#6b7280';
                    ?>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($expNom) ?>&background=<?= substr($expColor, 1) ?>&color=fff&size=60" 
                         class="rounded-circle me-3" width="60" height="60" alt="Avatar">
                    <div>
                        <strong class="d-block"><?= htmlspecialchars($expNom) ?></strong>
                        <small class="text-muted"><?= htmlspecialchars($message['expediteur_email']) ?></small>
                        <span class="badge ms-2" style="background-color: <?= $expColor ?>;">
                            <?= ucfirst($expRole) ?>
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar me-1"></i><?= date('d/m/Y à H:i', strtotime($message['created_at'])) ?>
                    </small>
                    <?php if ($message['lu'] && $message['lu_at']): ?>
                        <small class="text-success">
                            <i class="fas fa-check-double me-1"></i>Lu le <?= date('d/m/Y à H:i', strtotime($message['lu_at'])) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Destinataire -->
            <div class="mb-4">
                <?php 
                $destNom = $message['destinataire_prenom'] . ' ' . $message['destinataire_nom'];
                $destRole = $message['destinataire_role'];
                $destColor = $roleColors[$destRole] ?? '#6b7280';
                ?>
                <small class="text-muted">
                    <i class="fas fa-arrow-right me-1"></i>À : 
                    <strong><?= htmlspecialchars($destNom) ?></strong>
                    <span class="badge" style="background-color: <?= $destColor ?>;">
                        <?= ucfirst($destRole) ?>
                    </span>
                </small>
            </div>

            <!-- Contenu du message -->
            <div class="p-4 rounded" style="background: rgba(99, 102, 241, 0.05); border-left: 4px solid #6366f1;">
                <p class="mb-0" style="white-space: pre-wrap; line-height: 1.8;"><?= htmlspecialchars($message['contenu']) ?></p>
            </div>
        </div>
    </div>

    <!-- Actions spéciales pour les demandes d'assignation (admin uniquement) -->
    <?php if ($_SESSION['user_role'] === 'admin' && $message['sujet'] === "Demande d'assignation d'entreprise"): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header py-3 bg-success">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-check-circle me-2"></i>Actions de validation
                </h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-4">Traitez cette demande d'assignation d'entreprise :</p>
                <div class="d-flex gap-3">
                    <form action="<?= BASE_URL ?>/recruteurs/approve-request" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Approuver
                        </button>
                    </form>
                    <form action="<?= BASE_URL ?>/recruteurs/reject-request" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Rejeter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="d-flex justify-content-between">
        <a href="<?= BASE_URL ?>/messages" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
        <div class="d-flex gap-2">
            <?php if (!$isExpéditeur): ?>
                <a href="<?= BASE_URL ?>/messages/nouveau?reply=<?= $message['id'] ?>&destinataire=<?= $message['expediteur_id'] ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-reply me-2"></i>Répondre
                </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/messages/supprimer/<?= $message['id'] ?>" 
               class="btn btn-outline-danger"
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');">
                <i class="fas fa-trash me-2"></i>Supprimer
            </a>
        </div>
    </div>
</div>
