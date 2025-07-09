<?php
// Inclure votre fichier de connexion à la base de données
require_once 'config/database.php'; // Adaptez le chemin si besoin

// 1. Récupérer l'ID de l'article depuis l'URL
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Convertir en entier pour la sécurité

if ($article_id > 0) {
    // 2. Préparer et exécuter la requête pour récupérer l'article
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id"); // Utilisez PDO pour plus de sécurité
    $stmt->execute(['id' => $article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($article) {
        // L'article a été trouvé, affichez-le
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($article['titre']); ?> - Mon Blog de Foot</title>
            <link rel="stylesheet" href="path/to/bootstrap.min.css">
            <link rel="stylesheet" href="path/to/style.css">
        </head>
        <body>
            <div class="container">
                <h1 class="my-4"><?php echo htmlspecialchars($article['titre']); ?></h1>
                <p class="text-muted">Publié le <?php echo htmlspecialchars($article['date_publication']); ?></p>
                <img src="<?php echo htmlspecialchars($article['image']); ?>" class="img-fluid mb-4" alt="Image de l'article">
                <div class="article-content">
                    <?php echo nl2br(htmlspecialchars($article['contenu_complet'])); ?>
                    </div>
                <a href="index.php" class="btn btn-secondary mt-4">Retour aux articles</a>
            </div>
            <script src="path/to/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    } else {
        // Article non trouvé
        echo "<p>Désolé, l'article demandé n'a pas été trouvé.</p>";
    }
} else {
    // ID non valide
    echo "<p>ID d'article non valide.</p>";
}
?>
