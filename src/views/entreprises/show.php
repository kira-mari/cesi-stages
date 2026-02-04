<div class="container py-4 mb-5">

    <!-- Company Info Card -->
    <div class="card mb-5 border-0 shadow-sm" style="background: linear-gradient(145deg, hsl(var(--card)), rgba(20, 20, 35, 0.6)); border-top: 4px solid hsl(var(--primary));">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge bg-primary text-white mb-2 shadow-sm" style="font-weight: 500; letter-spacing: 0.5px;"><?= htmlspecialchars($entreprise['secteur']) ?></span>
                    <h1 class="display-5 fw-bold mb-1 text-white"><?= htmlspecialchars($entreprise['nom']) ?></h1>
                    <p class="text-white-50"><i class="fas fa-map-marker-alt me-2 text-primary"></i><?= htmlspecialchars($entreprise['adresse']) ?></p>
                </div>
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pilote'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-icon btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-h fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/entreprises/edit/<?= $entreprise['id'] ?>"><i class="fas fa-edit me-2"></i>Modifier</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/entreprises/delete/<?= $entreprise['id'] ?>" onclick="return confirm('Confirmer la suppression ?')"><i class="fas fa-trash-alt me-2"></i>Supprimer</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row mt-5">
                <div class="col-md-7 pe-md-5">
                    <h5 class="text-primary mb-3 text-uppercase fw-bold small letter-spacing-1">À propos de l'entreprise</h5>
                    <p class="text-muted" style="line-height: 1.8; font-size: 0.95rem;">
                        <?= nl2br(htmlspecialchars($entreprise['description'])) ?>
                    </p>
                </div>
                <div class="col-md-5 border-start border-secondary ps-md-4 mt-4 mt-md-0 dash-border">
                    <h5 class="text-primary mb-3 text-uppercase fw-bold small letter-spacing-1">Coordonnées</h5>
                    <ul class="list-unstyled">
                        <li class="mb-4">
                            <small class="d-block text-uppercase fw-bold text-white-50 fs-7 mb-1">Email</small>
                            <div class="d-flex align-items-center gap-2">
                                <div class="icon-box-sm bg-secondary bg-opacity-25 rounded-circle p-2">
                                    <i class="fas fa-envelope text-primary small"></i>
                                </div>
                                <a href="mailto:<?= htmlspecialchars($entreprise['email']) ?>" class="text-white text-decoration-none hover-primary transition-color">
                                    <?= htmlspecialchars($entreprise['email']) ?>
                                </a>
                            </div>
                        </li>
                        <li>
                            <small class="d-block text-uppercase fw-bold text-white-50 fs-7 mb-1">Téléphone</small>
                            <div class="d-flex align-items-center gap-2 text-white">
                                <div class="icon-box-sm bg-secondary bg-opacity-25 rounded-circle p-2">
                                    <i class="fas fa-phone text-primary small"></i>
                                </div>
                                <?= htmlspecialchars($entreprise['telephone']) ?>
                            </div>
                        </li>
                        <?php if (!empty($entreprise['site_web'])): ?>
                        <li class="mt-4">
                            <small class="d-block text-uppercase fw-bold text-white-50 fs-7 mb-1">Site Web</small>
                            <div class="d-flex align-items-center gap-2">
                                <div class="icon-box-sm bg-secondary bg-opacity-25 rounded-circle p-2">
                                    <i class="fas fa-globe text-primary small"></i>
                                </div>
                                <a href="<?= htmlspecialchars($entreprise['site_web']) ?>" target="_blank" class="text-white text-decoration-none hover-primary transition-color">
                                    <?= htmlspecialchars(parse_url($entreprise['site_web'], PHP_URL_HOST) ?? $entreprise['site_web']) ?> <i class="fas fa-external-link-alt small ms-1 opacity-50"></i>
                                </a>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Offers Section -->
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="mb-0 fw-bold">Offres de stage</h3>
            <span class="badge bg-secondary rounded-pill"><?= count($offres) ?></span>
        </div>
        
        <?php if (empty($offres)): ?>
            <div class="card p-5 text-center border-dashed">
                <div class="card-body">
                    <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fas fa-briefcase fa-2x text-muted"></i>
                    </div>
                    <p class="text-muted mb-0">Aucune offre disponible pour le moment.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($offres as $offre): ?>
                    <div class="col-md-6">
                        <a href="<?= BASE_URL ?>/offres/show/<?= $offre['id'] ?>" class="card text-decoration-none card-hover-effect border-0 bg-card-secondary h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="card-title mb-1 text-white fw-bold"><?= htmlspecialchars($offre['titre']) ?></h5>
                                        <p class="card-text text-muted small mb-0">
                                            <?= substr(htmlspecialchars($offre['description']), 0, 150) ?>...
                                        </p>
                                    </div>
                                    <span class="badge bg-dark border border-secondary text-white-50 fw-normal ms-2">
                                        <?= date('d/m/Y', strtotime($offre['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Unified Evaluation Section -->
    <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(145deg, hsl(var(--card)), rgba(20, 20, 35, 0.6));">
        <div class="card-header bg-transparent border-bottom border-secondary p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-bold text-white"><i class="fas fa-star text-warning me-2"></i>Avis et Évaluations</h3>
                
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'pilote', 'etudiant'])): ?>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="toggleReviewForm">
                        <i class="fas fa-pen-nib me-2"></i>Donner mon avis
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-body p-4">
            <!-- Review Form (Hidden by default, slides down) -->
            <div id="reviewFormContainer" style="display: none; overflow: hidden; transition: all 0.3s ease-in-out;">
                <div class="bg-dark bg-opacity-25 border border-primary border-opacity-25 rounded-3 p-4 mb-5">
                    <form action="<?= BASE_URL ?>/entreprises/evaluate/<?= $entreprise['id'] ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <h5 class="fw-bold mb-3 text-white">Partagez votre expérience</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label text-uppercase text-muted fw-bold small mb-2">Votre note</label>
                                <div class="rating-input d-flex flex-row-reverse justify-content-end gap-2">
                                    <input type="radio" id="s_star5" name="note" value="5" class="d-none" required /><label for="s_star5" title="Excellent"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="s_star4" name="note" value="4" class="d-none" /><label for="s_star4" title="Très bien"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="s_star3" name="note" value="3" class="d-none" /><label for="s_star3" title="Bien"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="s_star2" name="note" value="2" class="d-none" /><label for="s_star2" title="Moyen"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="s_star1" name="note" value="1" class="d-none" /><label for="s_star1" title="Mauvais"><i class="fas fa-star"></i></label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-uppercase text-muted fw-bold small mb-2">Votre commentaire</label>
                                <textarea name="commentaire" class="form-control border-secondary text-white" style="background-color: rgba(255, 255, 255, 0.08);" rows="3" placeholder="Qu'avez-vous pensé de l'ambiance, des missions, de l'équipe ?" required></textarea>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="button" class="btn btn-link text-muted me-2 text-decoration-none" id="cancelReview">Annuler</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Publier</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats & List -->
            <div class="row g-4">
                <!-- Global Stats (Left) -->
                <div class="col-md-4 col-lg-3 text-center">
                    <div class="p-4 bg-dark bg-opacity-25 rounded-3 border border-secondary mb-3">
                        <div class="display-1 fw-bold text-white mb-0"><?= number_format($moyenneEvaluations, 1) ?></div>
                        <div class="text-warning fs-4 mb-2">
                            <?php 
                            $score = round($moyenneEvaluations);
                            for($i = 1; $i <= 5; $i++) {
                                echo $i <= $score ? '<i class="fas fa-star"></i>' : '<i class="far fa-star opacity-25"></i>';
                            }
                            ?>
                        </div>
                        <p class="text-white-50 text-uppercase fw-bold letter-spacing-1 small mb-0"><?= count($evaluations) ?> avis</p>
                    </div>
                </div>

                <!-- Reviews List (Right) -->
                <div class="col-md-8 col-lg-9">
                    <?php if (empty($evaluations)): ?>
                        <div class="d-flex flex-column align-items-center justify-content-center bg-card-secondary rounded-3 border border-dashed p-5 text-muted">
                            <i class="far fa-comment-dots fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0 fw-medium">Aucun avis pour le moment.</p>
                            <p class="small mb-0">Soyez le premier à partager votre expérience !</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($evaluations as $eval): ?>
                                <div class="card bg-transparent border border-secondary p-3">
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="icon-box-sm bg-secondary bg-opacity-10 text-primary rounded-circle small">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <span class="text-white fw-bold small">
                                                <?= htmlspecialchars(($eval['user_prenom'] ?? '') . ' ' . ($eval['user_nom'] ?? '')) ?>
                                            </span>
                                            <div class="text-warning small ms-1">
                                                <?php for($i=1; $i<=5; $i++) echo $i <= $eval['note'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star opacity-25"></i>'; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted opacity-75" style="font-size: 0.8rem;"><?= date('d/m/Y', strtotime($eval['created_at'])) ?></small>
                                    </div>
                                    <p class="mb-0 text-white-50 ps-1" style="font-size: 0.95rem; line-height: 1.6;">
                                        "<?= htmlspecialchars($eval['commentaire']) ?>"
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleReviewForm');
    const cancelBtn = document.getElementById('cancelReview');
    const formContainer = document.getElementById('reviewFormContainer');
    
    if (toggleBtn && formContainer) {
        toggleBtn.addEventListener('click', function() {
            if (formContainer.style.display === 'none') {
                // Show with slide down effect
                formContainer.style.display = 'block';
                formContainer.style.height = 'auto'; // Get actual height
                const height = formContainer.clientHeight + 'px';
                formContainer.style.height = '0px';
                
                // Force reflow
                void formContainer.offsetWidth; 
                
                formContainer.style.height = height;
                
                // Reset height after transition
                setTimeout(() => {
                    formContainer.style.height = 'auto';
                }, 300);
                
                toggleBtn.classList.add('d-none'); // Hide button while form is open
            }
        });
    }
    
    if (cancelBtn && formContainer) {
        cancelBtn.addEventListener('click', function() {
            // Slide up
            formContainer.style.height = formContainer.clientHeight + 'px';
            void formContainer.offsetWidth; // Force reflow
            formContainer.style.height = '0px';
            
            setTimeout(() => {
                formContainer.style.display = 'none';
                if(toggleBtn) toggleBtn.classList.remove('d-none'); // Show button again
            }, 300);
        });
    }
});
</script>

<style>
/* ... existing styles ... */


<style>
/* Local styles for this view */
.fs-7 { font-size: 0.75rem; }
.letter-spacing-1 { letter-spacing: 1px; }

.card-hover-effect { 
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s; 
}
.card-hover-effect:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 10px 20px rgba(0,0,0,0.2); 
    background-color: hsl(var(--card) / 0.8) !important;
}

.bg-card-secondary {
    background-color: rgba(255, 255, 255, 0.03);
}

.icon-box-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.border-dashed {
    border-style: dashed !important;
    border-width: 2px !important;
    border-color: hsl(var(--border)) !important;
}

.progress-ring__circle {
    transition: stroke-dashoffset 0.35s;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
}

/* Star Rating Input */
.rating-input label {
    cursor: pointer;
    color: hsl(var(--muted-foreground));
    font-size: 1.5rem;
    transition: color 0.2s;
}
.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #fbbf24;
}

.dash-border {
    border-color: rgba(255, 255, 255, 0.1) !important;
}

@media (max-width: 768px) {
    .border-start { border-left: none !important; }
}

/* --- LIGHT MODE ADJUSTMENTS --- */
[data-theme="light"] .card[style*="linear-gradient"] {
    background: linear-gradient(145deg, #ffffff, #f1f5f9) !important;
    border: 1px solid #e2e8f0;
    border-top: 4px solid var(--primary) !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

[data-theme="light"] .card.bg-transparent {
    background-color: #ffffff !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

[data-theme="light"] .bg-card-secondary {
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0;
}

[data-theme="light"] .bg-dark {
    background-color: #f1f5f9 !important;
}

/* Invert colors for text elements on cards (excluding badges/buttons that keep their own bg) */
[data-theme="light"] h1.text-white,
[data-theme="light"] h2.text-white,
[data-theme="light"] h3.text-white,
[data-theme="light"] h5.text-white,
[data-theme="light"] p.text-white,
[data-theme="light"] div.text-white,
[data-theme="light"] span.text-white:not(.badge),
[data-theme="light"] a.text-white:not(.btn) {
    color: #1e293b !important; /* Slate 800 */
}

[data-theme="light"] .text-white-50 {
    color: #64748b !important; /* Slate 500 */
}

[data-theme="light"] .border-secondary {
    border-color: #cbd5e1 !important; /* Slate 300 */
}

[data-theme="light"] .dash-border {
    border-color: #e2e8f0 !important;
}

[data-theme="light"] textarea.text-white {
    color: #1e293b !important;
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
}

[data-theme="light"] .dropdown-menu {
    border: 1px solid #e2e8f0;
}

[data-theme="light"] .rating-input label {
    color: #cbd5e1;
}

[data-theme="light"] .rating-input label:hover,
[data-theme="light"] .rating-input input:checked ~ label {
    color: #f59e0b;
}

/* Adjust icon boxes in light mode */
[data-theme="light"] .icon-box-sm.bg-secondary {
    background-color: rgba(6, 182, 212, 0.1) !important; /* Cyan with low opacity */
}

/* Fix Badge Contrast in Light Mode: Turn solid primary into soft pastel with dark text */
[data-theme="light"] .badge.bg-primary {
    background-color: rgba(79, 70, 229, 0.1) !important; /* Very light Indigo */
    color: #4338ca !important; /* Dark Indigo text */
    border: 1px solid rgba(79, 70, 229, 0.2);
    box-shadow: none !important;
}
</style>
