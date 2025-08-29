<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LVDPA - La Voix Des Pères Abandonnés</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/homepagecss/home.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/homepagecss/header.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/homepagecss/sidebar.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/homepagecss/main.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/homepagecss/news-sidebar.css">
    <link rel="stylesheet" href="/LVDPA/public/CSS/homepagecss/footer.css">
</head>
<body>
    <div class="game-container">
        <div class="glare"></div>
        <?php include __DIR__ . '/../partials/header.php'; ?>
        <?php include __DIR__ . '/../partials/sidebar.php'; ?> 
        <?php include __DIR__ . '/../partials/main.php'; ?>
        <?php include __DIR__ . '/../partials/news-sidebar.php'; ?>
        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
    <!-- script pour le spa -->
    <script src="/LVDPA/public/js/spa.js"></script>
    <!-- script pour le message défilant -->
    <script src="/LVDPA/public/js/scrolling-text.js"></script>
    <!-- Updater pour la sidebar -->
    <script src="/LVDPA/public/js/homepagejs/sidebar-updater.js"></script>
</body>
</html>