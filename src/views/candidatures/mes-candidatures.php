<style>
/* Light Mode Support for Candidatures */
[data-theme="light"] .page-section h1, 
[data-theme="light"] .page-section h3.text-white, 
[data-theme="light"] .page-section .h2.text-white,
[data-theme="light"] .page-section .text-white {
    color: #1f2937 !important;
}

[data-theme="light"] .page-section p.text-muted {
    color: #6b7280 !important;
}

/* Badge Contrast */
[data-theme="light"] .badge.bg-primary {
    background-color: rgba(79, 70, 229, 0.1) !important;
    color: #4338ca !important;
    border: 1px solid rgba(79, 70, 229, 0.2);
}

/* Empty State Box */
[data-theme="light"] .bg-dark.rounded-circle {
    background-color: #f3f4f6 !important;
    border-color: #e5e7eb !important;
}
[data-theme="light"] .bg-dark.rounded-circle i {
    color: #9ca3af !important;
}

/* Candidature Card Light Mode */
[data-theme="light"] .candidature-card {
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

[data-theme="light"] .candidature-title a {
    color: #1f2937 !important;
}

[data-theme="light"] .candidature-company {
    color: #4f46e5 !important;
}

[data-theme="light"] .candidature-date {
    color: #6b7280 !important;
    background: #f3f4f6 !important;
    border-color: #e5e7eb !important;
}

/* Buttons in Card */
[data-theme="light"] .candidature-actions .btn-outline-light {
    color: #4b5563 !important;
    border-color: #e5e7eb !important;
}
[data-theme="light"] .candidature-actions .btn-outline-light:hover {
    background-color: #f3f4f6 !important;
    color: #1f2937 !important;
}

/* Modals Light Mode */
[data-theme="light"] .modal-content.bg-dark {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border: 1px solid #e5e7eb !important;
}
[data-theme="light"] .modal-header,
[data-theme="light"] .modal-footer {
    border-color: #e5e7eb !important;
}
[data-theme="light"] .modal-body .bg-black {
    background-color: #f9fafb !important;
    border-color: #e5e7eb !important;
}
[data-theme="light"] .modal-body p.text-white-50 {
    color: #4b5563 !important;
}
[data-theme="light"] .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}
[data-theme="light"] .modal-footer .btn-outline-light {
    color: #4b5563 !important;
    border-color: #e5e7eb !important;
} 
/* Hover effect for modal close button to be visible */
[data-theme="light"] .btn-close-white:hover {
    opacity: 0.7;
}

</style>

<div class="page-section">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-3">
            <h1 class="display-5 fw-bold mb-3">Mes Candidatures</h1>
            <p class="text-muted lead">Suivez l'avancement de vos demandes de stage.</p>
            <div class="mt-3">
                <span class="badge bg-primary rounded-pill px-4 py-2 text-uppercase tracking-wider">
                    <?= count($candidatures ?? []) ?> Candidature<?= count($candidatures ?? []) > 1 ? 's' : '' ?>
                </span>
            </div>
        </div>

        <?php if (empty($candidatures)): ?>
            <div class="text-center py-3">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-dark rounded-circle border border-secondary shadow-lg" style="width: 120px; height: 120px;">
                        <i class="fas fa-file-signature fa-4x text-muted opacity-50"></i>
                    </div>
                </div>
                <h3 class="h2 text-white mb-3">Aucune candidature</h3>
                <p class="text-muted mb-5 fs-5">Vous n'avez pas encore postulé à des offres de stage.</p>
                <a href="<?= BASE_URL ?>/offres" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg transition-transform hover-scale">
                    <i class="fas fa-search me-2"></i> Trouver un stage
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($candidatures as $candidature): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="candidature-card h-100">
                            <!-- Status Badge -->
                            <?php
                            $statusClass = 'secondary';
                            $statusIcon = 'fa-clock';
                            $statusLabel = $candidature['statut'];
                            $bgClass = 'bg-secondary';
                            $textClass = 'text-white';
                            
                            switch($candidature['statut']) {
                                case 'en_attente':
                                    $bgClass = 'bg-warning';
                                    $textClass = 'text-dark';
                                    $statusIcon = 'fa-hourglass-half';
                                    $statusLabel = 'En attente';
                                    break;
                                case 'acceptee':
                                    $bgClass = 'bg-success';
                                    $textClass = 'text-white';
                                    $statusIcon = 'fa-check';
                                    $statusLabel = 'Acceptée';
                                    break;
                                case 'refusee':
                                    $bgClass = 'bg-danger';
                                    $textClass = 'text-white';
                                    $statusIcon = 'fa-times';
                                    $statusLabel = 'Refusée';
                                    break;
                            }
                            ?>
                            <div class="candidature-status <?= $bgClass ?> <?= $textClass ?>">
                                <i class="fas <?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                            </div>

                            <!-- Date -->
                            <div class="candidature-date">
                                <i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($candidature['created_at'])) ?>
                            </div>

                            <!-- Content -->
                            <h3 class="candidature-title">
                                <a href="<?= BASE_URL ?>/offres/show/<?= $candidature['offre_id'] ?>" class="text-reset text-decoration-none stretched-link-title">
                                    <?= htmlspecialchars($candidature['offre_titre']) ?>
                                </a>
                            </h3>
                            
                            <div class="candidature-company">
                                <i class="fas fa-building"></i> <?= htmlspecialchars($candidature['entreprise_nom']) ?>
                            </div>

                            <!-- Actions -->
                            <div class="candidature-actions">
                                <?php if (!empty($candidature['cv_path'])): ?>
                                    <a href="<?= BASE_URL ?>/uploads/<?= $candidature['cv_path'] ?>" target="_blank" class="btn btn-outline-light btn-sm" title="Voir le CV">
                                        <i class="fas fa-file-pdf"></i> CV
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-outline-secondary btn-sm disabled opacity-50"><i class="fas fa-file-pdf"></i> CV</span>
                                <?php endif; ?>

                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalLettre<?= $candidature['id'] ?>" title="Lettre de motivation">
                                    <i class="fas fa-align-left"></i> Lettre
                                </button>
                                
                                <a href="<?= BASE_URL ?>/offres/show/<?= $candidature['offre_id'] ?>" class="btn btn-primary btn-sm" title="Voir l'offre">
                                    <i class="fas fa-eye"></i> Offre
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modals (Placed outside grid to avoid layout issues) -->
            <?php foreach ($candidatures as $candidature): ?>
                <div class="modal fade" id="modalLettre<?= $candidature['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-dark border border-secondary text-white">
                            <div class="modal-header border-secondary">
                                <h5 class="modal-title">
                                    <i class="fas fa-file-alt me-2 text-primary"></i> Lettre de motivation
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="p-3 bg-black bg-opacity-25 rounded border border-secondary">
                                    <p style="white-space: pre-wrap;" class="mb-0 text-white-50"><?= htmlspecialchars($candidature['lettre_motivation']) ?></p>
                                </div>
                            </div>
                            <div class="modal-footer border-secondary">
                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
