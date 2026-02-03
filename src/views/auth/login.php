<?php
// Vue partielle intégrée dans le layout principal
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth-modern.css">

<!-- Overlay qui recouvre tout le site (header/footer inclus) -->
<div class="auth-overlay">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
            <div class="auth-icon">
                <i class="fas fa-cube"></i>
            </div>
            <h1 class="auth-title">Bienvenue</h1>
            <p class="auth-subtitle">Connectez-vous pour continuer sur CesiStages</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="auth-alert">
                <ul style="margin:0; padding-left:1.2rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="auth-form-group">
                <label for="email" class="auth-label">Adresse email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="auth-input" placeholder="nom@cesi.fr" required>
                </div>
            </div>

            <div class="auth-form-group">
                <label for="password" class="auth-label">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="auth-input" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="far fa-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="auth-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    <span class="custom-checkbox">
                        <i class="fas fa-check" style="font-size: 0.7rem; color: white;"></i>
                    </span>
                    Se souvenir de moi
                </label>
                <a href="#" class="forgot-password">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-auth-primary">
                Se connecter <i class="fas fa-arrow-right"></i>
            </button>

            <div class="divider">ou</div>

            <?php if (defined('GOOGLE_OAUTH_ENABLED') && GOOGLE_OAUTH_ENABLED): ?>
            <a href="<?= BASE_URL ?>/auth/google" class="btn-auth-google">
                <i class="fab fa-google"></i> Continuer avec Google
            </a>
            <?php else: ?>
            <button type="button" class="btn-auth-google">
                <i class="fab fa-google"></i> Continuer avec Google
            </button>
            <?php endif; ?>

            <div class="auth-footer">
                Pas encore de compte ? <a href="<?= BASE_URL ?>/register" class="auth-link">S'inscrire</a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>

</body>
</html>
