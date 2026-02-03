<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                        <h2 class="card-title">Vérifiez votre email</h2>
                        <p class="text-muted">Un code de validation a été envoyé à <strong><?= htmlspecialchars($email) ?></strong></p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Erreurs :</strong>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label for="code" class="form-label">Code de validation</label>
                            <input 
                                type="text" 
                                class="form-control text-center fs-4 letter-spacing" 
                                id="code" 
                                name="code" 
                                placeholder="000000" 
                                maxlength="6"
                                pattern="\d{6}"
                                inputmode="numeric"
                                required
                            >
                            <small class="text-muted d-block mt-2">Entrez les 6 chiffres reçus par email</small>
                        </div>

                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-check me-2"></i> Valider mon email
                        </button>
                    </form>

                    <div class="text-center border-top pt-3 mt-3">
                        <p class="text-muted mb-2">Vous n'avez pas reçu le code ?</p>
                        <a href="<?= BASE_URL ?>/auth/resend-validation-code" class="btn btn-link btn-sm">
                            <i class="fas fa-redo me-1"></i> Renvoyer le code
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= BASE_URL ?>/auth/login" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Retour à la connexion
                        </a>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <small>
                    <strong>Info :</strong> Le code est valable 30 minutes. Vous avez 3 tentatives pour le saisir correctement.
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing {
        letter-spacing: 8px;
        font-weight: bold;
    }
</style>
