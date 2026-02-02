<div class="auth-container">
    <div class="auth-wrapper">
        <div class="card auth-card shadow-lg">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <div class="auth-icon-wrapper mb-3">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1 class="h3 font-weight-bold mb-2">Créer un compte</h1>
                    <p class="text-muted">Rejoignez CesiStages pour accéder aux offres.</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 pl-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= BASE_URL ?>/register" method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input 
                                        type="text" 
                                        id="prenom" 
                                        name="prenom" 
                                        placeholder="John"
                                        required
                                        autocomplete="given-name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input 
                                        type="text" 
                                        id="nom" 
                                        name="nom" 
                                        placeholder="Doe"
                                        required
                                        autocomplete="family-name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="nom@exemple.com"
                                required
                                autocomplete="email"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="8 caractères minimum"
                                required
                                autocomplete="new-password"
                                class="form-control"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password')" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="••••••••"
                                required
                                autocomplete="new-password"
                                class="form-control"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-actions mb-4">
                        <button type="submit" class="btn btn-primary btn-block w-100 py-3 font-weight-bold">
                            S'inscrire <i class="fas fa-user-plus ml-2"></i>
                        </button>
                    </div>

                    <?php if (defined('GOOGLE_OAUTH_ENABLED') && GOOGLE_OAUTH_ENABLED): ?>
                        <div class="form-actions mb-3">
                            <a href="<?= BASE_URL ?>/auth/google" class="btn btn-outline-danger btn-block w-100 py-3 font-weight-bold">
                                <i class="fab fa-google mr-2"></i> S'inscrire / Se connecter avec Google
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <p class="text-muted text-sm mb-0">
                            Déjà un compte ? 
                            <a href="<?= BASE_URL ?>/login" class="text-primary font-weight-bold text-decoration-none ml-1">Se connecter</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
