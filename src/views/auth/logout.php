<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déconnexion...</title>
    <script>
        // Nettoyage de l'historique de chat de l'utilisateur connecté pour sécurité
        // (Empêche qu'un autre utilisateur sur le même poste puisse lire l'historique via localStorage)
        <?php if ($userId): ?>
        try {
            const key = 'cesistages_chatbot_history_v1_user_<?= $userId ?>';
            localStorage.removeItem(key);
            console.log('Historique de chat sécurisé (suppression locale).');
        } catch (e) {
            console.error('Erreur nettoyage chat:', e);
        }
        <?php endif; ?>

        // Redirection vers l'accueil
        window.location.href = '<?= BASE_URL ?>';
    </script>
</head>
<body>
    <p>Déconnexion en cours...</p>
</body>
</html>