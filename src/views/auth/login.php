<div class="auth-container">
    <div class="auth-wrapper">
        <div class="card auth-card shadow-lg">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <div class="auth-icon-wrapper mb-3">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h1 class="h3 font-weight-bold mb-2">Bienvenue</h1>
                    <p class="text-muted">Entrez vos identifiants pour accéder à votre espace.</p>
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
                
                <form action="<?= BASE_URL ?>/login" method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="form-group mb-4">
                        <label for="email" class="form-label">Adresse email</label>
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
                            >
                        </div>
                    </div>
                    
                    <div class="form-group mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Mot de passe</label>
                            <a href="#" class="text-sm font-weight-medium text-primary text-decoration-none">Mot de passe oublié ?</a>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                                class="form-control"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password')" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-actions mb-4">
                        <button type="submit" class="btn btn-primary btn-block w-100 py-3 font-weight-bold">
                            Se connecter <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>

                    <?php if (defined('GOOGLE_OAUTH_ENABLED') && GOOGLE_OAUTH_ENABLED): ?>
                        <div class="form-actions mb-3">
                            <a href="<?= BASE_URL ?>/auth/google" class="btn btn-outline-danger btn-block w-100 py-3 font-weight-bold">
                                <i class="fab fa-google mr-2"></i> Se connecter avec Google
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <p class="text-muted text-sm mb-0">
                            Pas encore de compte ? 
                            <a href="<?= BASE_URL ?>/register" class="text-primary font-weight-bold text-decoration-none ml-1">Créer un compte</a>
                        </p>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-muted-light p-4 text-center">
                <p class="mb-2 text-sm font-weight-bold text-uppercase text-muted">Comptes de démonstration</p>
                <div class="demo-accounts-grid">
                    <div class="demo-badge admin" title="Cliquez pour copier" onclick="fillLogin('admin@cesi.fr', 'admin123')">
                        <span class="role">Admin</span>
                        <span class="creds">admin / admin123</span>
                    </div>
                    <div class="demo-badge pilote" title="Cliquez pour copier" onclick="fillLogin('pilote@cesi.fr', 'pilote123')">
                        <span class="role">Pilote</span>
                        <span class="creds">pilote / pilote123</span>
                    </div>
                    <div class="demo-badge etudiant" title="Cliquez pour copier" onclick="fillLogin('etudiant@cesi.fr', 'etudiant123')">
                        <span class="role">Étudiant</span>
                        <span class="creds">etudiant / etudiant123</span>
                    </div>
                </div>
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

function fillLogin(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
