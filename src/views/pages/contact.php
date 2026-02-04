<section class="page-content contact-page">
    <div class="container">
        <div class="row g-5 align-items-center">
            <!-- Left Side: Content & Info -->
            <div class="col-md-6 pr-lg-5">
                <span class="text-primary font-weight-bold text-uppercase small tracking-wide">Contact</span>
                <h1 class="display-5 font-weight-bold mt-2 mb-4">Dites-nous comment nous pouvons vous aider</h1>
                <p class="text-muted lead mb-5">
                    Nous sommes là pour répondre à toutes vos questions. N'hésitez pas à remplir le formulaire, 
                    ou à utiliser les coordonnées ci-dessous pour nous contacter directement.
                </p>

                <div class="contact-info-list mt-5">
                    <div class="contact-info-item d-flex align-items-center mb-4">
                        <div class="icon-container">
                            <i class="far fa-envelope"></i>
                        </div>
                        <a href="mailto:contact@cesi-stages.fr">contact@cesi-stages.fr</a>
                    </div>
                    
                    <div class="contact-info-item d-flex align-items-center mb-4">
                        <div class="icon-container">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <span>+33 1 23 45 67 89</span>
                    </div>

                    <div class="contact-info-item d-flex align-items-start">
                        <div class="icon-container mt-1">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <span>123 Avenue de l'Innovation,<br>75001 Paris, France</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="col-md-6">
                <form action="<?= BASE_URL ?>/contact/submit" method="POST" class="contact-form-clean">
                    <div class="mb-4">
                        <label for="fullname" class="form-label text-muted small font-weight-bold">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="fullname" name="fullname" required placeholder="">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label text-muted small font-weight-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required placeholder="">
                    </div>
                    
                    <div class="mb-4">
                        <label for="subject" class="form-label text-muted small font-weight-bold">Sujet <span class="text-danger">*</span></label>
                        <select class="form-select form-control form-control-lg" id="subject" name="subject" required>
                            <option value="" selected disabled>Sélectionnez un sujet</option>
                            <option value="stage">Recherche de stage</option>
                            <option value="partenariat">Partenariat entreprise</option>
                            <option value="support">Support technique</option>
                            <option value="compte">Problème de compte</option>
                            <option value="autre">Autres</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="form-label text-muted small font-weight-bold">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg form-textarea" id="message" name="message" rows="5" required placeholder=""></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
