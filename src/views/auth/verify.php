<?php
// Vue partielle intégrée dans le layout principal
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth-modern.css">

<style>
    /* Full screen overlay to cover navbar */
    .auth-overlay {
        position: relative;
        min-height: 100vh;
        z-index: 1100; /* Try to keep it high, but layout matters more */
        background-color: #05050a;
        background-image: 
            radial-gradient(circle at top left, rgba(139, 92, 246, 0.15), transparent 50%),
            radial-gradient(circle at bottom right, rgba(236, 72, 153, 0.15), transparent 50%);
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: flex-start; /* Changed from center to allow explicit top spacing */
        padding-top: 140px; /* Push down significantly below navbar */
        padding-bottom: 3rem;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .auth-link-back {
        display: inline-flex; 
        align-items: center; 
        gap: 0.5rem; 
        color: rgba(255,255,255,0.5); 
        text-decoration: none; 
        font-size: 0.9rem; 
        transition: all 0.2s;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }
    .auth-link-back:hover {
        color: white;
        background: rgba(255,255,255,0.05);
    }
    
    /* Ensure alerts are always on top and visible */
    .auth-alert {
        position: relative;
        z-index: 10002;
    }

    /* Fix potential interference with main layout */
    main {
        padding: 0 !important;
        margin: 0 !important;
    }
</style>

<!-- Overlay qui recouvre tout le site (header/footer inclus) -->
<div class="auth-overlay">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon" style="font-size: 2rem; background: transparent; box-shadow: none; margin-bottom: 0;">
                    🔒
                </div>
                <h1 class="auth-title">Vérification requise</h1>
                <p class="auth-subtitle">Un code a été envoyé à <br><strong><?= htmlspecialchars($_SESSION['verify_email'] ?? '') ?></strong></p>
            </div>
        
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="auth-alert alert-danger" style="background: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.3); color: #ff8fa3; padding: 0.8rem; border-radius: 10px; margin-bottom: 15px; font-size: 0.9rem; text-align: center;">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['flash_error'] ?>
                    <?php unset($_SESSION['flash_error']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="auth-alert alert-success" style="background: rgba(40, 167, 69, 0.15); border: 1px solid rgba(40, 167, 69, 0.3); color: #75b798; padding: 0.8rem; border-radius: 10px; margin-bottom: 15px; font-size: 0.9rem; text-align: center;">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['flash_success'] ?>
                    <?php unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/verify/submit">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="auth-form-group">
                    <label for="code" class="auth-label">Code de vérification</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="text" id="code" name="code" class="auth-input" placeholder="123456" maxlength="6" required autocomplete="off" style="text-align: center; letter-spacing: 5px; font-size: 1.2rem; font-family: monospace;">
                    </div>
                </div>

                <!-- Timer (Minimalist) -->
                <div id="timer-container" style="text-align: center; margin: 0.5rem 0 1.5rem 0; font-size: 0.85rem; color: rgba(255,255,255,0.5);">
                    <i class="far fa-clock"></i> Temps restant : <span id="timer" style="font-weight: 600; color: #ff6b6b;">--:--</span>
                </div>

                <button type="submit" class="btn-auth-primary">
                    Vérifier <i class="fas fa-check-circle"></i>
                </button>
            </form>
            
            <div class="auth-footer" style="margin-top: 2rem; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1.5rem;">
                <p style="color: rgba(255,255,255,0.5); font-size: 0.9rem; margin-bottom: 1rem;">
                    Code non reçu ? <a href="<?= BASE_URL ?>/verify/resend" class="auth-link" style="color: var(--auth-accent);">Renvoyer</a>
                </p>
                
                <a href="<?= BASE_URL ?>/login" class="auth-link-back">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Récupération du timestamp d'expiration depuis PHP
    const expiresAt = <?= $expires_at ?> * 1000; // PHP timestamp is in seconds, JS needs ms

    function updateTimer() {
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance < 0) {
            document.getElementById("timer").innerHTML = "EXPIRÉ";
            document.getElementById("timer").style.color = "red";
            // Optionnel : Désactiver le bouton ou rediriger
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("timer").innerHTML = 
            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
            (seconds < 10 ? "0" + seconds : seconds);
    }

    setInterval(updateTimer, 1000);
    updateTimer(); // Initial call
</script>
