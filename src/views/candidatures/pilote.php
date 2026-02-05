<style>
    .gradient-text {
        background: linear-gradient(135deg, hsl(var(--primary)), hsl(var(--secondary)));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .glass-panel {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid hsl(var(--border) / 0.5);
        border-radius: var(--radius);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .custom-table {
        margin-bottom: 0;
        --bs-table-bg: transparent;
        --bs-table-accent-bg: transparent;
        color: hsl(var(--foreground));
    }
    .custom-table > :not(caption) > * > * {
        background-color: transparent !important;
        box-shadow: none !important;
    }
    .custom-table thead th {
        background: rgba(0, 0, 0, 0.2);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid hsl(var(--border));
        padding: 1.25rem 1.5rem;
        color: hsl(var(--muted-foreground));
    }
    .custom-table tbody td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid hsl(var(--border) / 0.5);
        color: hsl(var(--foreground));
        vertical-align: middle;
    }
    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }
    .custom-table tbody tr {
        transition: background-color 0.2s;
    }
    .custom-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    .avatar-initials {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, hsl(var(--primary) / 0.8), hsl(var(--secondary) / 0.8));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .status-badge::before {
        content: '';
        display: block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .status-badge.success {
        background: rgba(34, 197, 94, 0.1);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    .status-badge.success::before { background-color: #4ade80; }
    
    .status-badge.danger {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .status-badge.danger::before { background-color: #f87171; }
    
    .status-badge.warning {
        background: rgba(234, 179, 8, 0.1);
        color: #facc15;
        border: 1px solid rgba(234, 179, 8, 0.2);
    }
    .status-badge.warning::before { background-color: #facc15; }
    
    .status-badge.secondary {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }
    .status-badge.secondary::before { background-color: #94a3b8; }
    
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255,255,255,0.03);
        color: hsl(var(--muted-foreground));
        border: 1px solid transparent;
    }
    .action-btn:hover {
        background: hsl(var(--primary) / 0.1);
        color: hsl(var(--primary));
        border-color: hsl(var(--primary) / 0.2);
        transform: translateY(-2px);
    }
    .company-icon {
        color: hsl(var(--muted-foreground));
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }
</style>

<div class="page-section">
    <div class="container">
        
        <div class="page-header">
            <h1>Suivi des Candidatures</h1>
            <p>Analysez l'activité de vos étudiants en temps réel.</p>
            <div class="d-inline-block bg-glass px-3 py-2 rounded-pill mt-2 border border-secondary border-opacity-25">
                <span class="text-muted small text-uppercase fw-bold">Total :</span>
                <span class="fw-bold ms-2"><?= count($candidatures) ?></span>
            </div>
        </div>

    <div class="glass-panel overflow-hidden">
        <?php if (empty($candidatures)): ?>
            <div class="text-center py-5 px-4 h-100 d-flex flex-column justify-content-center align-items-center">
                <div class="mb-4 position-relative d-inline-block">
                    <div class="position-absolute top-0 start-50 translate-middle pointer-events-none" 
                         style="width: 100px; height: 100px; background: hsl(var(--primary) / 0.2); filter: blur(40px); border-radius: 50%;"></div>
                    <i class="fas fa-clipboard-list fa-4x text-muted relative z-10" style="opacity: 0.5;"></i>
                </div>
                <h3 class="h4 mb-2">Aucune donnée disponible</h3>
                <p class="text-muted mb-0">Vos étudiants n'ont pas encore postulé à des offres.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Offre & Entreprise</th>
                            <th>Date de candidature</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatures as $candidature): ?>
                            <?php 
                                $initials = strtoupper(substr($candidature['etudiant_prenom'], 0, 1) . substr($candidature['etudiant_nom'], 0, 1));
                                $statusClass = match($candidature['statut']) {
                                    'acceptee' => 'success',
                                    'refusee' => 'danger',
                                    'en_attente' => 'warning',
                                    default => 'secondary'
                                };
                                $statusLabel = match($candidature['statut']) {
                                    'acceptee' => 'Acceptée',
                                    'refusee' => 'Refusée',
                                    'en_attente' => 'En attente',
                                    default => ucfirst(str_replace('_', ' ', $candidature['statut']))
                                };
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-initials text-shadow-sm">
                                            <?= $initials ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-foreground">
                                                <?= htmlspecialchars($candidature['etudiant_prenom'] . ' ' . $candidature['etudiant_nom']) ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($candidature['etudiant_email']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="<?= BASE_URL ?>/offres/show/<?= $candidature['offre_id'] ?>" 
                                           class="fw-semibold text-foreground text-decoration-none hover-primary mb-1">
                                            <?= htmlspecialchars($candidature['offre_titre']) ?>
                                        </a>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="far fa-building me-2 opacity-75"></i>
                                            <?= htmlspecialchars($candidature['entreprise_nom']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="far fa-calendar-alt me-2 opacity-75"></i>
                                        <?= date('d/m/Y', strtotime($candidature['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if (!empty($candidature['lettre_motivation'])): ?>
                                            <button type="button" 
                                                    class="action-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalMotivation<?= $candidature['id'] ?>"
                                                    title="Lettre de motivation">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($candidature['cv_path'])): ?>
                                            <a href="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($candidature['cv_path']) ?>" 
                                               class="action-btn" 
                                               target="_blank" 
                                               data-bs-toggle="tooltip" 
                                               title="Voir le CV">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= BASE_URL ?>/offres/show/<?= $candidature['offre_id'] ?>" 
                                           class="action-btn"
                                           data-bs-toggle="tooltip"
                                           title="Voir l'offre">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    </div>
</div>

<!-- Modals outside everything to avoid z-index/backdrop-filter issues -->
<?php if (!empty($candidatures)): ?>
    <?php foreach ($candidatures as $candidature): ?>
        <?php if (!empty($candidature['lettre_motivation'])): ?>
            <?php 
                $initials = strtoupper(substr($candidature['etudiant_prenom'], 0, 1) . substr($candidature['etudiant_nom'], 0, 1));
            ?>
            <!-- Modal Lettre de Motivation -->
            <div class="modal fade" id="modalMotivation<?= $candidature['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content text-white" style="background: rgba(15, 15, 26, 0.95); backdrop-filter: blur(20px); border: 1px solid hsl(var(--border)); border-radius: var(--radius); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title font-monospace text-uppercase small text-muted mb-1">Candidature</h5>
                                <div class="h5 mb-0 fw-bold"><?= htmlspecialchars($candidature['offre_titre']) ?></div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body pt-4">
                            <div class="d-flex align-items-center mb-4 p-3 rounded-3" style="background: hsl(var(--card)); border: 1px solid hsl(var(--border));">
                                <div class="avatar-initials me-3 rounded-circle shadow-lg" style="width: 48px; height: 48px; font-size: 1.1rem;">
                                    <?= $initials ?>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5"><?= htmlspecialchars($candidature['etudiant_prenom'] . ' ' . $candidature['etudiant_nom']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($candidature['etudiant_email']) ?></div>
                                </div>
                                <div class="ms-auto text-end d-none d-sm-block">
                                    <span class="badge bg-dark border border-secondary text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($candidature['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <h6 class="text-uppercase text-muted small fw-bold mb-3"><i class="fas fa-quote-left me-2 text-primary"></i>Lettre de motivation</h6>
                            <div class="p-4 rounded-3 position-relative" style="background: hsl(var(--background)); border: 1px solid hsl(var(--border)); min-height: 200px;">
                                <div class="text-white" style="white-space: pre-line; line-height: 1.8; font-size: 0.95rem;">
                                    <?= htmlspecialchars($candidature['lettre_motivation']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                            <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>