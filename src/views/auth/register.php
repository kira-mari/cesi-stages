<?php
// On inclut le CSS spécifique auth
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth-modern.css">

<!-- Overlay qui recouvre tout le site (header/footer inclus) -->
<div class="auth-overlay">
    <div class="auth-container">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-subtitle">Rejoignez-nous pour accéder aux offres de stage</p>
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

        <form action="<?= BASE_URL ?>/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="auth-form-group">
                    <label for="prenom" class="auth-label">Prénom</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="prenom" name="prenom" class="auth-input" placeholder="Jean" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                    </div>
                </div>
                <div class="auth-form-group">
                    <label for="nom" class="auth-label">Nom</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="nom" name="nom" class="auth-input" placeholder="Dupont" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Choix du rôle -->
            <div class="auth-form-group">
                <label class="auth-label">Je suis</label>
                <div class="role-selector" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: 0.5rem;">
                    <label class="role-option" style="display: flex; flex-direction: column; align-items: center; padding: 1rem; border: 2px solid rgba(255,255,255,0.1); border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                        <input type="radio" name="role" value="etudiant" style="display: none;" <?= ($_POST['role'] ?? 'etudiant') === 'etudiant' ? 'checked' : '' ?>>
                        <i class="fas fa-user-graduate" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #10b981;"></i>
                        <span style="font-size: 0.85rem; font-weight: 500;">Étudiant</span>
                    </label>
                    <label class="role-option" style="display: flex; flex-direction: column; align-items: center; padding: 1rem; border: 2px solid rgba(255,255,255,0.1); border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                        <input type="radio" name="role" value="recruteur" style="display: none;" <?= ($_POST['role'] ?? '') === 'recruteur' ? 'checked' : '' ?>>
                        <i class="fas fa-briefcase" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #8b5cf6;"></i>
                        <span style="font-size: 0.85rem; font-weight: 500;">Recruteur</span>
                    </label>
                    <label class="role-option" style="display: flex; flex-direction: column; align-items: center; padding: 1rem; border: 2px solid rgba(255,255,255,0.1); border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                        <input type="radio" name="role" value="pilote" style="display: none;" <?= ($_POST['role'] ?? '') === 'pilote' ? 'checked' : '' ?>>
                        <i class="fas fa-chalkboard-teacher" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #0ea5e9;"></i>
                        <span style="font-size: 0.85rem; font-weight: 500;">Pilote</span>
                    </label>
                </div>
            </div>

            <div class="auth-form-group">
                <label for="email" class="auth-label">Adresse email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="auth-input" placeholder="nom@cesi.fr" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>

            <div class="auth-form-group">
                <label for="password" class="auth-label">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="auth-input" placeholder="••••••••" required oninput="checkPasswordStrength()">
                    <button type="button" class="toggle-password" onclick="togglePassword('password', 'toggleIcon')">
                        <i class="far fa-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
                <!-- Password requirements feedback -->
                <div id="password-feedback" class="password-strength-container">
                    <div class="strength-meter-bar-container">
                        <div id="strength-bar" class="strength-meter-bar"></div>
                    </div>
                    <div class="criteria-list">
                        <div id="length-check" class="criteria-item"><i class="fas fa-circle"></i> 8 caractères minimum</div>
                        <div id="uppercase-check" class="criteria-item"><i class="fas fa-circle"></i> 1 majuscule</div>
                        <div id="lowercase-check" class="criteria-item"><i class="fas fa-circle"></i> 1 minuscule</div>
                        <div id="number-check" class="criteria-item"><i class="fas fa-circle"></i> 1 chiffre</div>
                        <div id="special-check" class="criteria-item"><i class="fas fa-circle"></i> 1 caractère spécial</div>
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
                <div id="match-feedback" class="password-strength-container">
                     <div id="match-check" class="criteria-item">
                        <i class="fas fa-circle"></i> Les mots de passe correspondent
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-auth-primary" style="margin-top: 1rem;">
                S'inscrire <i class="fas fa-arrow-right"></i>
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
                Vous avez déjà un compte ? <a href="<?= BASE_URL ?>/login" class="auth-link">Se connecter</a>
            </div>
        </form>
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
    const strengthBar = document.getElementById('strength-bar');
    
    // Elements
    const lengthCheck = document.getElementById('length-check');
    const uppercaseCheck = document.getElementById('uppercase-check');
    const lowercaseCheck = document.getElementById('lowercase-check');
    const numberCheck = document.getElementById('number-check');
    const specialCheck = document.getElementById('special-check');

    if (password.length > 0) {
        feedback.classList.add('visible');
    } else {
        feedback.classList.remove('visible');
    }

    let score = 0;

    // Helper function to update UI
    const updateCriteria = (element, isValid) => {
        const icon = element.querySelector('i');
        if (isValid) {
            element.classList.add('valid');
            element.classList.remove('invalid');
            icon.className = 'fas fa-check-circle';
            score++;
        } else {
            element.classList.remove('valid');
            element.classList.add('invalid');
            icon.className = 'fas fa-circle';
        }
    };

    // Validations
    updateCriteria(lengthCheck, password.length >= 8);
    updateCriteria(uppercaseCheck, /[A-Z]/.test(password));
    updateCriteria(lowercaseCheck, /[a-z]/.test(password));
    updateCriteria(numberCheck, /[0-9]/.test(password));
    updateCriteria(specialCheck, /[\W_]/.test(password));

    // Update Strength Bar
    const percentage = (score / 5) * 100;
    strengthBar.style.width = percentage + '%';

    if (score <= 2) {
        strengthBar.style.backgroundColor = 'var(--strength-weak)';
    } else if (score <= 4) {
        strengthBar.style.backgroundColor = 'var(--strength-medium)';
    } else {
        strengthBar.style.backgroundColor = 'var(--strength-strong)';
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
        feedback.classList.add('visible');
        
        if (password === confirm) {
            matchCheck.classList.add('valid');
            matchCheck.classList.remove('invalid');
            icon.className = 'fas fa-check-circle';
        } else {
            matchCheck.classList.remove('valid');
            matchCheck.classList.add('invalid');
            icon.className = 'fas fa-times-circle'; // Or circle if strict match style
        }
    } else {
        feedback.classList.remove('visible');
    }
}

// Gestion de la sélection de rôle
document.addEventListener('DOMContentLoaded', function() {
    const roleOptions = document.querySelectorAll('.role-option');
    
    function updateRoleSelection() {
        roleOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            if (radio.checked) {
                option.style.borderColor = '#6366f1';
                option.style.background = 'rgba(99, 102, 241, 0.1)';
                option.style.transform = 'scale(1.02)';
            } else {
                option.style.borderColor = 'rgba(255,255,255,0.1)';
                option.style.background = 'transparent';
                option.style.transform = 'scale(1)';
            }
        });
    }
    
    roleOptions.forEach(option => {
        option.addEventListener('click', updateRoleSelection);
    });
    
    // Initialiser
    updateRoleSelection();
});
</script>

    </div>
</div>
