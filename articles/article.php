<?php
// article.php - Page d'affichage d'un article complet

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php'; // Assurez-vous que le chemin est correct pour article.php

$article = null;
$errorMessage = '';

// Vérifier si un ID d'article est passé dans l'URL
if (isset($_GET['id'])) {
    $article_id = (int)$_GET['id']; // Convertir en entier pour la sécurité

    try {
        // Récupérer l'article complet
        // Assurez-vous que votre table d'articles s'appelle 'posts'
        $stmt = $pdo->prepare("SELECT p.id, p.title, p.content, p.image_url, p.created_at, u.username AS author_name
                             FROM posts p
                             JOIN users u ON p.user_id = u.id
                             WHERE p.id = :id");
        $stmt->execute(['id' => $article_id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            $errorMessage = "Désolé, l'article demandé n'a pas été trouvé.";
        }

    } catch (PDOException $e) {
        $errorMessage = "Erreur lors du chargement de l'article : " . $e->getMessage();
    }
} else {
    $errorMessage = "Aucun ID d'article spécifié.";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article['title']) : 'Article non trouvé'; ?> - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"> <style>
        /* Styles spécifiques à la page d'article si besoin */
        .article-full-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container my-5">
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php elseif ($article): ?>
            <article>
                <h1 class="mb-4"><?php echo htmlspecialchars($article['title']); ?></h1>
                <p class="text-muted">Par <?php echo htmlspecialchars($article['author_name']); ?> le <?php echo date('d/m/Y', strtotime($article['created_at'])); ?></p>
                <?php if ($article['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($article['title']); ?>">
                <?php endif; ?>
                <div class="article-full-content mb-4">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
                <a href="index.php" class="btn btn-secondary">Retour aux articles</a>
            </article>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
