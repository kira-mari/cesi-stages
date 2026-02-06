<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($description) ? htmlspecialchars($description) : 'Plateforme de recherche de stages pour les étudiants CESI' ?>">
    <meta name="keywords" content="stage, CESI, étudiant, entreprise, offre de stage">
    <meta name="author" content="Web4All">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
    <meta property="og:description" content="<?= isset($description) ? htmlspecialchars($description) : 'Plateforme de recherche de stages pour les étudiants CESI' ?>">
    <meta property="og:type" content="website">
    
    <title><?= htmlspecialchars($title) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= ASSETS_URL ?>/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css?v=<?= time() ?>">
    <!-- Chatbot CSS (dans le dossier public/css car le vhost pointe déjà sur public) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/chatbot.css?v=<?= time() ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?= isset($bodyClass) ? htmlspecialchars($bodyClass) : '' ?>">
    <!-- Header -->
    <?php require APP_PATH . '/views/partials/header.php'; ?>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="flash-message flash-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="flash-message flash-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_info'])): ?>
        <div class="flash-message flash-info">
            <i class="fas fa-info-circle"></i>
            <?= htmlspecialchars($_SESSION['flash_info']) ?>
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php unset($_SESSION['flash_info']); ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <?php require APP_PATH . '/views/partials/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript -->
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
    <!-- Variables globales pour le chatbot -->
    <script>
        window.CHATBOT_USER_ID = <?= isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null' ?>;
        window.BASE_URL = '<?= BASE_URL ?>';
    </script>
    <!-- Chatbot JS (dans le dossier public/js car le vhost pointe déjà sur public) -->
    <script src="<?= BASE_URL ?>/js/chatbot.js"></script>
    
    <!-- Badge messages non lus -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <script>
        // Charger le compteur de messages non lus
        function loadMessageCount() {
            fetch(window.BASE_URL + '/messages/count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('message-badge');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline';
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(() => {});
        }
        // Charger au démarrage et toutes les 30 secondes
        document.addEventListener('DOMContentLoaded', function() {
            loadMessageCount();
            setInterval(loadMessageCount, 30000);
        });
    </script>
    <?php endif; ?>
</body>
</html>
