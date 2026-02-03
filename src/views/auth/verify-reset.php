<?php
// Vue vérification code reset
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth-modern.css">

<div class="auth-overlay">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="auth-title">Vérification</h1>
                <p class="auth-subtitle">Entrez le code à 6 chiffres reçu par email.</p>
                <?php if(isset($_SESSION['reset_expires'])): ?>
                    <p class="verification-timer" id="timer">Expire dans <span id="countdown">05:00</span></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="auth-alert fade-in">
                    <ul style="margin:0; padding-left:1.2rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="auth-alert success fade-in">
                    <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/forgot-password/verify-code" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="auth-form-group">
                    <label for="code" class="auth-label">Code de vérification</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="text" id="code" name="code" class="auth-input example-code" placeholder="123456" maxlength="6" pattern="[0-9]{6}" required autocomplete="off">
                    </div>
                </div>

                <?php if (isset($_SESSION['reset_attempts']) && $_SESSION['reset_attempts'] > 0): ?>
                    <p style="text-align: center; color: #ffab40; font-size: 0.9rem; margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-triangle"></i> Il vous reste <?= (3 - $_SESSION['reset_attempts']) + 1 ?> tentative(s).
                    </p>
                <?php endif; ?>

                <button type="submit" class="btn-auth-primary">
                    Vérifier <i class="fas fa-check" style="margin-left: 0.5rem;"></i>
                </button>
            </form>

            <div class="auth-footer">
                <p>Pas reçu ? <a href="<?= BASE_URL ?>/forgot-password" class="auth-link">Renvoyer</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Timer script (Optional visual enhancement)
    <?php if(isset($_SESSION['reset_expires'])): ?>
    const expiresAt = <?= $_SESSION['reset_expires'] ?> * 1000;
    
    function updateTimer() {
        const now = new Date().getTime();
        const distance = expiresAt - now;
        
        if (distance < 0) {
            document.getElementById("countdown").innerHTML = "Expiré";
            document.getElementById("timer").style.color = "#ff4444";
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById("countdown").innerHTML = 
            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
            (seconds < 10 ? "0" + seconds : seconds);
    }
    
    setInterval(updateTimer, 1000);
    updateTimer();
    <?php endif; ?>
</script>
