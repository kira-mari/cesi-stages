<?php
// Vue nouveau mot de passe
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth-modern.css">

<div class="auth-overlay">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-lock-open"></i>
                </div>
                <h1 class="auth-title">Nouveau mot de passe</h1>
                <p class="auth-subtitle">Sécurisez votre compte avec un nouveau mot de passe fort.</p>
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

            <form action="<?= BASE_URL ?>/forgot-password/update" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="auth-form-group">
                    <label for="password" class="auth-label">Nouveau mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="auth-input" placeholder="••••••••" required oninput="checkPasswordStrength()">
                        <button type="button" class="toggle-password" onclick="togglePassword('password', 'toggleIcon')">
                            <i class="far fa-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                     <!-- Password requirements feedback -->
                    <div id="password-feedback" style="margin-top: 0.5rem; font-size: 0.8rem; height: 0; overflow: hidden; transition: height 0.3s ease;">
                        <div id="length-check" style="color: rgba(255,255,255,0.5); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-circle" style="font-size: 0.4rem;"></i> 8 caractères minimum
                        </div>
                    </div>
                </div>

                <div class="auth-form-group">
                    <label for="confirm_password" class="auth-label">Confirmer le mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="auth-input" placeholder="••••••••" required oninput="checkPasswordMatch()">
                         <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', 'toggleIconConfirm')">
                            <i class="far fa-eye-slash" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                    <div id="match-feedback" style="margin-top: 0.5rem; font-size: 0.8rem; height: 0; overflow: hidden; transition: height 0.3s ease;">
                         <div id="match-check" style="color: rgba(255,255,255,0.5); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-circle" style="font-size: 0.4rem;"></i> Les mots de passe correspondent
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-auth-primary">
                    Modifier le mot de passe <i class="fas fa-save" style="margin-left: 0.5rem;"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
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

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const feedback = document.getElementById('password-feedback');
    const lengthCheck = document.getElementById('length-check');
    const icon = lengthCheck.querySelector('i');

    if (password.length > 0) {
        feedback.style.height = '20px';
    } else {
        feedback.style.height = '0';
    }

    if (password.length >= 8) {
        lengthCheck.style.color = '#4ade80'; // Green
        icon.className = 'fas fa-check-circle';
        icon.style.color = '#4ade80';
    } else {
        lengthCheck.style.color = 'rgba(255,255,255,0.5)';
        icon.className = 'fas fa-circle';
        icon.style.color = 'rgba(255,255,255,0.5)';
    }

    checkPasswordMatch(); // Re-check match when password changes
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const feedback = document.getElementById('match-feedback');
    const matchCheck = document.getElementById('match-check');
    const icon = matchCheck.querySelector('i');

    if (confirm.length > 0) {
        feedback.style.height = '20px';
        
        if (password === confirm) {
            matchCheck.style.color = '#4ade80';
            icon.className = 'fas fa-check-circle';
            icon.style.color = '#4ade80';
        } else {
            matchCheck.style.color = '#f87171'; // Red
            icon.className = 'fas fa-times-circle';
            icon.style.color = '#f87171';
        }
    } else {
        feedback.style.height = '0';
    }
}
</script>
