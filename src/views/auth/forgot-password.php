<?php
// Vue mot de passe oublié
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth-modern.css">

<div class="auth-overlay">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h1 class="auth-title">Mot de passe oublié ?</h1>
                <p class="auth-subtitle">Entrez votre adresse email pour recevoir un code.
                    <br><span style="color: rgba(255,255,255,0.6); font-size: 0.85em;">(Code valide 5 minutes)</span>
                </p>
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

            <form action="<?= BASE_URL ?>/forgot-password" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="auth-form-group">
                    <label for="email" class="auth-label">Adresse email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="auth-input" placeholder="nom@cesi.fr" required>
                    </div>
                </div>

                <button type="submit" class="btn-auth-primary">
                    Envoyer le code <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
                </button>
            </form>

            <div class="auth-footer">
                <p>Vous avez retrouvé la mémoire ? <a href="<?= BASE_URL ?>/login" class="auth-link">Se connecter</a></p>
            </div>
        </div>
    </div>
</div>
