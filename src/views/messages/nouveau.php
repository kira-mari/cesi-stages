<div class="container py-5" style="margin-top: 80px; max-width: 800px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/messages">Messagerie</a></li>
            <li class="breadcrumb-item active">Nouveau message</li>
        </ol>
    </nav>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
            <h4 class="mb-0 text-white">
                <i class="fas fa-pen me-2"></i>
                <?php if ($messageReponse): ?>
                    Répondre à <?= htmlspecialchars($messageReponse['expediteur_prenom'] . ' ' . $messageReponse['expediteur_nom']) ?>
                <?php else: ?>
                    Nouveau message
                <?php endif; ?>
            </h4>
        </div>
        <div class="card-body p-4">
            <?php if ($messageReponse): ?>
                <div class="alert alert-light mb-4">
                    <small class="text-muted">
                        <i class="fas fa-reply me-1"></i>En réponse à :
                    </small>
                    <p class="mb-0 mt-2 fst-italic">
                        "<?= htmlspecialchars(substr($messageReponse['contenu'], 0, 200)) ?><?= strlen($messageReponse['contenu']) > 200 ? '...' : '' ?>"
                    </p>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/messages/envoyer" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Destinataire -->
                <div class="mb-4">
                    <label for="destinataire_id" class="form-label fw-bold">
                        <i class="fas fa-user me-1"></i>Destinataire
                    </label>
                    <?php if ($destinataire): ?>
                        <input type="hidden" name="destinataire_id" value="<?= $destinataire['id'] ?>">
                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(99, 102, 241, 0.1);">
                            <?php 
                            $roleColors = [
                                'admin' => '#dc2626',
                                'pilote' => '#0ea5e9',
                                'recruteur' => '#8b5cf6',
                                'etudiant' => '#10b981'
                            ];
                            $destColor = $roleColors[$destinataire['role']] ?? '#6b7280';
                            ?>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($destinataire['prenom'] . '+' . $destinataire['nom']) ?>&background=<?= substr($destColor, 1) ?>&color=fff&size=40" 
                                 class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                            <div>
                                <strong><?= htmlspecialchars($destinataire['prenom'] . ' ' . $destinataire['nom']) ?></strong>
                                <span class="badge ms-2" style="background-color: <?= $destColor ?>;">
                                    <?= ucfirst($destinataire['role']) ?>
                                </span>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($destinataire['email']) ?></small>
                            </div>
                        </div>
                    <?php else: ?>
                        <select name="destinataire_id" id="destinataire_id" class="form-select" required>
                            <option value="">Sélectionnez un destinataire...</option>
                            <?php 
                            $groupedDestinataires = [];
                            foreach ($destinataires as $d) {
                                $groupedDestinataires[$d['role']][] = $d;
                            }
                            $roleLabels = [
                                'etudiant' => 'Étudiants',
                                'pilote' => 'Pilotes',
                                'recruteur' => 'Recruteurs',
                                'admin' => 'Administrateurs'
                            ];
                            foreach ($roleLabels as $role => $label):
                                if (!empty($groupedDestinataires[$role])):
                            ?>
                                <optgroup label="<?= $label ?>">
                                    <?php foreach ($groupedDestinataires[$role] as $d): ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= htmlspecialchars($d['prenom'] . ' ' . $d['nom']) ?> (<?= $d['email'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Sujet -->
                <div class="mb-4">
                    <label for="sujet" class="form-label fw-bold">
                        <i class="fas fa-tag me-1"></i>Sujet
                    </label>
                    <input type="text" name="sujet" id="sujet" class="form-control" required
                           placeholder="Objet du message"
                           value="<?= $messageReponse ? 'Re: ' . htmlspecialchars($messageReponse['sujet']) : '' ?>">
                </div>

                <!-- Contenu -->
                <div class="mb-4">
                    <label for="contenu" class="form-label fw-bold">
                        <i class="fas fa-align-left me-1"></i>Message
                    </label>
                    <textarea name="contenu" id="contenu" class="form-control" rows="8" required
                              placeholder="Écrivez votre message ici..."></textarea>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>/messages" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
