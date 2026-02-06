<div class="page-section" style="padding-top: 1.5rem;">
    <div class="container">
        
        <div class="mb-4">
            <a href="<?= BASE_URL ?>/offres" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left"></i> Retour aux offres
            </a>
        </div>

        <div class="two-column-layout">
            <!-- Main Content -->
            <div class="main-column">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h1 class="h2 mb-2"><?= htmlspecialchars($offre['titre']) ?></h1>
                                <a href="<?= BASE_URL ?>/entreprises/<?= $offre['entreprise_id'] ?>" class="text-primary font-weight-bold h5 text-decoration-none">
                                    <i class="fas fa-building mr-2"></i> <?= htmlspecialchars($offre['entreprise_nom']) ?>
                                </a>
                            </div>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'etudiant'): ?>
                                <?php if (!isset($_SESSION['user_role_pending']) || $_SESSION['user_role_pending'] !== 'recruteur'): ?>
                                    <?php 
                                        $btnClass = $inWishlist ? 'btn-primary text-white' : 'btn-outline-secondary';
                                        $iconClass = $inWishlist ? 'fas fa-star' : 'far fa-star';
                                        $title = $inWishlist ? 'Retirer de la wishlist' : 'Ajouter à la wishlist';
                                        $action = $inWishlist ? 'removeFromWishlist' : 'addToWishlist';
                                    ?>
                                    <a href="<?= BASE_URL ?>/offres/<?= $action ?>/<?= $offre['id'] ?>" 
                                       class="btn btn-icon <?= $btnClass ?> wishlist-btn" 
                                       title="<?= $title ?>"
                                       data-id="<?= $offre['id'] ?>"
                                       data-in-wishlist="<?= $inWishlist ? 'true' : 'false' ?>">
                                        <i class="<?= $iconClass ?>"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="grid-2-cols mb-4 py-3 border-top border-bottom">
                            <div>
                                <p class="mb-2 text-muted"><i class="fas fa-map-marker-alt width-20"></i> Lieu</p>
                                <p class="font-weight-bold"><?= htmlspecialchars($offre['entreprise_adresse'] ?? 'Non précisé') ?></p>
                            </div>
                            <div>
                                <p class="mb-2 text-muted"><i class="far fa-clock width-20"></i> Durée</p>
                                <p class="font-weight-bold"><?= $offre['duree'] ?> mois</p>
                            </div>
                            <div>
                                <p class="mb-2 text-muted"><i class="fas fa-coins width-20"></i> Rémunération</p>
                                <p class="font-weight-bold"><?= number_format($offre['remuneration'] ?? 0, 0, ',', ' ') ?> € / mois</p>
                            </div>
                            <div>
                                <p class="mb-2 text-muted"><i class="far fa-calendar-alt width-20"></i> Dates</p>
                                <p class="font-weight-bold">
                                    <?php if($offre['date_debut']): ?>
                                        <?= date('d/m/Y', strtotime($offre['date_debut'])) ?>
                                    <?php else: ?>
                                        Dès que possible
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h5 mb-3">Description du poste</h3>
                            <div class="text-muted line-height-relaxed">
                                <?= nl2br(htmlspecialchars($offre['description'])) ?>
                            </div>
                        </div>

                        <div>
                            <h3 class="h5 mb-3">Compétences requises</h3>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $comp = json_decode($offre['competences'], true);
                                if ($comp && is_array($comp)): 
                                    foreach($comp as $c): ?>
                                        <span class="skill-tag"><?= htmlspecialchars($c) ?></span>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-column">
                <?php if (isset($_SESSION['user_role'])): ?>
                    <?php if ($_SESSION['user_role'] === 'etudiant'): ?>
                        <div class="card mb-4 sticky-top">
                            <div class="card-header">
                                <h3 class="h5 m-0">Postuler</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($aPostule): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle mr-2"></i> Vous avez déjà postulé.
                                    </div>
                                <?php else: ?>
                                    <form action="<?= BASE_URL ?>/offres/postuler/<?= $offre['id'] ?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold mb-2">Lettre de motivation <span class="text-danger">*</span></label>
                                            <textarea name="lettre_motivation" class="form-textarea" rows="3" placeholder="Présentez-vous et expliquez pourquoi ce poste vous intéresse..." required></textarea>
                                        </div>
                                        
                                        <div class="form-group mb-4">
                                            <label class="form-label fw-bold mb-2">CV (PDF, DOC) <span class="text-danger">*</span></label>
                                            <div class="custom-file-upload">
                                                <input type="file" name="cv" id="cv_upload" class="form-file-input" required onchange="updateFileName(this)">
                                                <label for="cv_upload" class="file-upload-label">
                                                    <div class="icon-container">
                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                    </div>
                                                    <span class="file-name-text" id="file-name-display">Cliquez pour importer votre CV</span>
                                                    <span class="file-formats">Formats : PDF, DOC, DOCX (Max 5 Mo)</span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                            <i class="fas fa-paper-plane mr-2"></i> Envoyer ma candidature
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif ($canAdminister): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="h5 m-0">Administration</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Candidatures</span>
                                    <span class="badge badge-primary rounded-pill"><?= $nbCandidatures ?></span>
                                </div>
                                <hr class="border-secondary my-3">
                                <div class="d-grid gap-2">
                                    <a href="<?= BASE_URL ?>/offres/edit/<?= $offre['id'] ?>" class="btn btn-outline w-100">Modifier</a>
                                    <a href="<?= BASE_URL ?>/offres/delete/<?= $offre['id'] ?>" class="btn btn-danger w-100" onclick="return confirm(''Êtes-vous sûr de vouloir supprimer cette offre ?'');">Supprimer</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                     <div class="card mb-4 sticky-top" style="top: 2rem; z-index: 10;">
                        <div class="card-body text-center">
                            <h3 class="h5 mb-3">Intéressé ?</h3>
                            <p class="text-muted mb-4">Connectez-vous pour postuler à cette offre.</p>
                            <a href="<?= BASE_URL ?>/login" class="btn btn-primary w-100">Se connecter</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
function updateFileName(input) {
    const label = document.getElementById('file-name-display');
    const uploadArea = input.nextElementSibling;
    const maxSize = 5 * 1024 * 1024; // 5MB

    // Reset error state
    uploadArea.classList.remove('file-upload-error');
    const iconContainer = uploadArea.querySelector('.icon-container');
    const icon = uploadArea.querySelector('i');
    const formatsText = uploadArea.querySelector('.file-formats');
    
    // Existing remove btn?
    const existingRemoveBtn = uploadArea.querySelector('.remove-file-btn');
    if (existingRemoveBtn) existingRemoveBtn.remove();

    if (input.files && input.files.length > 0) {
        if (input.files[0].size > maxSize) {
            // Error state
            input.value = ""; 
            uploadArea.classList.add('file-upload-error');
            label.textContent = 'Fichier trop volumineux !';
            label.className = 'file-name-text text-danger fw-bold';
            formatsText.textContent = 'La taille maximale est de 5 Mo';
            formatsText.className = 'file-formats text-danger';
            icon.className = 'fas fa-exclamation-circle';
            iconContainer.style.backgroundColor = 'hsl(var(--destructive) / 0.1)';
            iconContainer.style.borderColor = 'hsl(var(--destructive))';
            setTimeout(() => { uploadArea.classList.remove('file-upload-error'); }, 600);
            return;
        }

        // Success state
        label.textContent = input.files[0].name;
        label.className = 'file-name-text text-primary';
        formatsText.textContent = 'Formats : PDF, DOC, DOCX (Max 5 Mo)';
        formatsText.className = 'file-formats';
        icon.className = 'fas fa-check-circle';
        iconContainer.style.backgroundColor = 'hsl(142, 71%, 45%, 0.1)';
        iconContainer.style.borderColor = 'hsl(142, 71%, 45%)';
        icon.style.color = '#16a34a'; 

        // Add Remove Button
        const removeBtn = document.createElement('div');
        removeBtn.className = 'remove-file-btn';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.title = "Supprimer le fichier";
        removeBtn.onclick = (e) => {
            e.preventDefault(); 
            e.stopPropagation();
            input.value = "";
            updateFileName(input);
        };
        uploadArea.appendChild(removeBtn);

    } else {
        // Default state
        label.textContent = 'Cliquez pour importer votre CV';
        label.className = 'file-name-text';
        formatsText.textContent = 'Formats : PDF, DOC, DOCX (Max 5 Mo)';
        formatsText.className = 'file-formats';
        icon.className = 'fas fa-cloud-upload-alt';
        iconContainer.style.backgroundColor = '';
        iconContainer.style.borderColor = '';
        icon.style.color = ''; 
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Drag and Drop Logic
    const dropZoneLabel = document.querySelector('.file-upload-label');
    const fileInput = document.getElementById('cv_upload');

    if (dropZoneLabel && fileInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZoneLabel.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZoneLabel.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZoneLabel.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZoneLabel.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZoneLabel.classList.remove('dragover');
        }

        dropZoneLabel.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                updateFileName(fileInput);
            }
        }
    }

    // Select all wishlist buttons
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');
    const BASE_URL = '<?= BASE_URL ?>';

    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault(); // Prevent default link navigation
            e.stopPropagation(); // Stop bubbling

            const id = btn.getAttribute('data-id');
            const isInWishlist = btn.getAttribute('data-in-wishlist') === 'true';
            
            const action = isInWishlist ? 'removeFromWishlist' : 'addToWishlist';
            const url = `${BASE_URL}/offres/${action}/${id}`;

            try {
                btn.style.opacity = '0.7';

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success) {
                        // Toggle state
                        const newState = !isInWishlist;
                        btn.setAttribute('data-in-wishlist', newState ? 'true' : 'false');
                        
                        // Update visual
                        const icon = btn.querySelector('i');
                        if (newState) {
                            // Added
                            btn.classList.remove('btn-outline-secondary');
                            btn.classList.add('btn-primary', 'text-white');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            btn.setAttribute('title', 'Retirer de la wishlist');
                            
                            showToast("Offre ajoutée à votre liste", "success");
                        } else {
                            // Removed
                            btn.classList.remove('btn-primary', 'text-white');
                            btn.classList.add('btn-outline-secondary');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            btn.setAttribute('title', 'Ajouter à la wishlist');
                            
                            showToast("Offre retirée de votre liste", "info");
                        }
                    }
                }
            } catch (err) {
                console.error("Erreur wishlist:", err);
            } finally {
                btn.style.opacity = '1';
                // Update href for fallback
                const nextAction = btn.getAttribute('data-in-wishlist') === 'true' ? 'removeFromWishlist' : 'addToWishlist';
                btn.setAttribute('href', `${BASE_URL}/offres/${nextAction}/${id}`);
            }
        });
    });

    // Helper for simple toast notification
    function showToast(message, type = 'info') {
        const existingToasts = document.querySelectorAll('.flash-message');
        if (existingToasts.length >= 3) {
            existingToasts[0].remove();
        }

        const toast = document.createElement('div');
        toast.className = `flash-message flash-${type}`;
        
        const currentToasts = document.querySelectorAll('.flash-message');
        let topOffset = 90; 
        if (currentToasts.length > 0) {
            topOffset += (currentToasts.length * 70); 
        }
        
        toast.style.top = `${topOffset}px`;
        
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        
        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            ${message}
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s, transform 0.3s';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>

