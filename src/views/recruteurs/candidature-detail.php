<div class="container py-5" style="margin-top: 80px; max-width: 1400px;">
    <!-- Breadcrumb centré -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/recruteur/candidatures">Candidatures</a></li>
            <li class="breadcrumb-item active">Détail</li>
        </ol>
    </nav>

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

    <!-- Titre principal centré -->
    <div class="text-center mb-5">
        <h1 class="h2 fw-bold mb-2">
            <i class="fas fa-clipboard-check me-2 text-primary"></i>Candidature de <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
        </h1>
        <p class="text-muted">
            Pour l'offre <strong><?= htmlspecialchars($offre['titre']) ?></strong> chez <strong><?= htmlspecialchars($entreprise['nom']) ?></strong>
        </p>
        <div class="mt-3">
            <?php
            $badgeClass = [
                'en_attente' => 'bg-warning text-dark',
                'acceptee' => 'bg-success',
                'refusee' => 'bg-danger'
            ][$candidature['statut']] ?? 'bg-secondary';
            $statutLabel = [
                'en_attente' => 'En attente',
                'acceptee' => 'Acceptée',
                'refusee' => 'Refusée'
            ][$candidature['statut']] ?? $candidature['statut'];
            ?>
            <span class="badge <?= $badgeClass ?> fs-5 px-4 py-2"><?= $statutLabel ?></span>
            <small class="d-block mt-2 text-muted">
                <i class="fas fa-calendar-plus me-1"></i>Reçue le <?= date('d/m/Y à H:i', strtotime($candidature['created_at'])) ?>
            </small>
        </div>
    </div>

    <!-- Actions rapides centrées -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
                <div class="card-body p-4">
                    <h5 class="text-white text-center mb-4"><i class="fas fa-gavel me-2"></i>Actions</h5>
                    <form action="<?= BASE_URL ?>/recruteur/candidature/update/<?= $candidature['id'] ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <?php if ($candidature['statut'] !== 'acceptee'): ?>
                                <button type="submit" name="statut" value="acceptee" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-check me-2"></i>Accepter
                                </button>
                            <?php endif; ?>
                            <?php if ($candidature['statut'] !== 'refusee'): ?>
                                <button type="submit" name="statut" value="refusee" class="btn btn-danger btn-lg px-4">
                                    <i class="fas fa-times me-2"></i>Refuser
                                </button>
                            <?php endif; ?>
                            <?php if ($candidature['statut'] !== 'en_attente'): ?>
                                <button type="submit" name="statut" value="en_attente" class="btn btn-outline-light btn-lg px-4">
                                    <i class="fas fa-undo me-2"></i>Remettre en attente
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        <!-- Colonne gauche : Candidat -->
        <div class="col-lg-4 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-center py-3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-user me-2"></i>Candidat</h5>
                </div>
                <div class="card-body text-center py-4">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($etudiant['prenom'] . '+' . $etudiant['nom']) ?>&background=6366f1&color=fff&size=120" 
                         class="rounded-circle mb-3 shadow" width="120" height="120" alt="Avatar">
                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h4>
                    
                    <!-- Bouton contacter -->
                    <div class="mb-4">
                        <a href="<?= BASE_URL ?>/messages/nouveau?destinataire=<?= $etudiant['id'] ?>" 
                           class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Contacter cet étudiant
                        </a>
                    </div>
                    
                    <div class="mt-4 text-start">
                        <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: rgba(99, 102, 241, 0.1);">
                            <i class="fas fa-envelope text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <a href="mailto:<?= $etudiant['email'] ?>" class="text-decoration-none"><?= htmlspecialchars($etudiant['email']) ?></a>
                            </div>
                        </div>
                        
                        <?php if (!empty($etudiant['telephone'])): ?>
                            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: rgba(99, 102, 241, 0.1);">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Téléphone</small>
                                    <a href="tel:<?= $etudiant['telephone'] ?>" class="text-decoration-none"><?= htmlspecialchars($etudiant['telephone']) ?></a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($etudiant['age'])): ?>
                            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: rgba(99, 102, 241, 0.1);">
                                <i class="fas fa-birthday-cake text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Âge</small>
                                    <span><?= $etudiant['age'] ?> ans</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($etudiant['adresse'])): ?>
                            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: rgba(99, 102, 241, 0.1);">
                                <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Adresse</small>
                                    <span><?= htmlspecialchars($etudiant['adresse']) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($etudiant['bio'])): ?>
                    <div class="card-footer bg-light">
                        <small><strong>Bio:</strong> <?= htmlspecialchars($etudiant['bio']) ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Colonne droite : Offre + Documents -->
        <div class="col-lg-8 col-xl-7">
            <!-- Offre de stage -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-briefcase me-2"></i>Offre de stage</h5>
                </div>
                <div class="card-body p-4">
                    <h4 class="fw-bold"><?= htmlspecialchars($offre['titre']) ?></h4>
                    <p class="text-muted mb-3">
                        <i class="fas fa-building me-1"></i><?= htmlspecialchars($entreprise['nom']) ?>
                        <?php if (!empty($entreprise['adresse'])): ?>
                            <span class="ms-3"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($entreprise['adresse']) ?></span>
                        <?php endif; ?>
                    </p>
                    
                    <p class="mb-4"><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
                    
                    <div class="row g-3">
                        <?php if (!empty($offre['duree'])): ?>
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background: rgba(14, 165, 233, 0.1);">
                                    <i class="fas fa-clock fa-2x text-info mb-2"></i>
                                    <p class="mb-0 fw-bold"><?= $offre['duree'] ?> mois</p>
                                    <small class="text-muted">Durée</small>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($offre['remuneration'])): ?>
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background: rgba(16, 185, 129, 0.1);">
                                    <i class="fas fa-euro-sign fa-2x text-success mb-2"></i>
                                    <p class="mb-0 fw-bold"><?= number_format($offre['remuneration'], 0, ',', ' ') ?> €</p>
                                    <small class="text-muted">Par mois</small>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($offre['date_debut'])): ?>
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background: rgba(168, 85, 247, 0.1);">
                                    <i class="fas fa-calendar-alt fa-2x text-purple mb-2" style="color: #a855f7;"></i>
                                    <p class="mb-0 fw-bold"><?= date('d/m/Y', strtotime($offre['date_debut'])) ?></p>
                                    <small class="text-muted">Date de début</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($offre['competences'])): ?>
                        <?php $competences = json_decode($offre['competences'], true); ?>
                        <?php if ($competences && is_array($competences)): ?>
                            <div class="mt-4">
                                <strong><i class="fas fa-tools me-1"></i>Compétences requises:</strong>
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    <?php foreach ($competences as $comp): ?>
                                        <span class="skill-tag"><?= htmlspecialchars($comp) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lettre de motivation -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-file-alt me-2"></i>Lettre de motivation</h5>
                    <?php if (!empty($candidature['lettre_motivation'])): ?>
                        <span class="badge bg-white text-success"><i class="fas fa-check me-1"></i>Fournie</span>
                    <?php else: ?>
                        <span class="badge bg-white text-secondary"><i class="fas fa-times me-1"></i>Non fournie</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($candidature['lettre_motivation'])): ?>
                        <div class="p-4 rounded" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(249, 115, 22, 0.05) 100%); border-left: 4px solid #f59e0b;">
                            <p class="mb-0 fst-italic" style="line-height: 1.8;"><?= nl2br(htmlspecialchars($candidature['lettre_motivation'])) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                            <p class="text-muted mb-0">Le candidat n'a pas fourni de lettre de motivation.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- CV -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-file-pdf me-2"></i>Curriculum Vitae (CV)</h5>
                    <?php if (!empty($candidature['cv_path'])): ?>
                        <span class="badge bg-white text-success"><i class="fas fa-check me-1"></i>Fourni</span>
                    <?php else: ?>
                        <span class="badge bg-white text-secondary"><i class="fas fa-times me-1"></i>Non fourni</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($candidature['cv_path'])): ?>
                        <?php 
                        $cvPathRaw = $candidature['cv_path'];
                        if (strpos($cvPathRaw, 'uploads/') !== 0) {
                            $cvPathRaw = 'uploads/' . $cvPathRaw;
                        }
                        $cvUrl = BASE_URL . '/' . htmlspecialchars($cvPathRaw);
                        $extension = strtolower(pathinfo($candidature['cv_path'], PATHINFO_EXTENSION));
                        ?>
                        
                        <!-- Boutons d'action -->
                        <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                            <a href="<?= $cvUrl ?>" target="_blank" class="btn btn-danger btn-lg">
                                <i class="fas fa-external-link-alt me-2"></i>Ouvrir dans un nouvel onglet
                            </a>
                            <a href="<?= $cvUrl ?>" download class="btn btn-outline-danger btn-lg">
                                <i class="fas fa-download me-2"></i>Télécharger
                            </a>
                        </div>
                        
                        <!-- Lecteur PDF intégré -->
                        <div class="border rounded" style="height: 700px; overflow: hidden;">
                            <?php if ($extension === 'pdf'): ?>
                                <object data="<?= $cvUrl ?>" type="application/pdf" width="100%" height="100%" style="border: none;">
                                    <embed src="<?= $cvUrl ?>" type="application/pdf" width="100%" height="100%">
                                        <div class="text-center py-5">
                                            <p>Votre navigateur ne supporte pas l'affichage des PDF.</p>
                                            <a href="<?= $cvUrl ?>" target="_blank" class="btn btn-danger">
                                                <i class="fas fa-external-link-alt me-1"></i>Cliquez ici pour ouvrir le CV
                                            </a>
                                        </div>
                                    </embed>
                                </object>
                            <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                <div class="text-center p-4" style="height: 100%; overflow: auto;">
                                    <img src="<?= $cvUrl ?>" alt="CV du candidat" class="img-fluid shadow" style="max-height: 100%;">
                                </div>
                            <?php elseif (in_array($extension, ['doc', 'docx'])): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-file-word fa-5x text-primary mb-4"></i>
                                    <p class="text-muted fs-5">Fichier Word - Aperçu non disponible</p>
                                    <a href="<?= $cvUrl ?>" download class="btn btn-primary btn-lg">
                                        <i class="fas fa-download me-2"></i>Télécharger pour lire
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-file fa-5x text-muted mb-4"></i>
                                    <p class="text-muted fs-5">Aperçu non disponible pour ce format</p>
                                    <a href="<?= $cvUrl ?>" download class="btn btn-secondary btn-lg">
                                        <i class="fas fa-download me-2"></i>Télécharger
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-pdf fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                            <p class="text-muted mb-0">Le candidat n'a pas fourni de CV.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton retour centré -->
    <div class="text-center mt-5">
        <a href="<?= BASE_URL ?>/recruteur/candidatures" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Retour aux candidatures
        </a>
    </div>
</div>
