<?php
// articles.php - Page listant tous les articles

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données
// Le chemin est 'config/db.php' car articles.php est à la racine de blog_football/
require_once 'config/db.php';

$allArticles = [];
$errorMessage = '';

try {
    // Récupérer tous les articles, triés par date de création (du plus récent au plus ancien)
    // Joindre avec la table 'users' pour obtenir le nom de l'auteur
    // Assurez-vous que votre table d'articles s'appelle 'posts'
    $stmtArticles = $pdo->query("SELECT p.id, p.title, p.content, p.image_url, p.created_at, u.username AS author_name
                                 FROM posts p
                                 JOIN users u ON p.user_id = u.id
                                 ORDER BY p.created_at DESC");
    $allArticles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorMessage = "Erreur lors du chargement des articles : " . $e->getMessage();
}

// Fonction utilitaire pour tronquer le texte (réutilisée de index.php)
function truncateText($text, $maxLength = 200) {
    if (strlen($text) > $maxLength) {
        $text = substr($text, 0, $maxLength);
        $text = substr($text, 0, strrpos($text, ' ')); // Évite de couper un mot en plein milieu
        $text .= '...';
    }
    return $text;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Articles - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"> <!-- Chemin vers votre CSS personnalisé -->
    <style>
        /* Styles personnalisés pour les cartes d'articles */
        .article-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
            height: 100%; /* Assure que toutes les cartes ont la même hauteur */
        }
        .article-card:hover {
            transform: translateY(-5px);
        }
        .article-card img {
            height: 220px; /* Hauteur fixe pour les images des articles */
            object-fit: cover;
            width: 100%;
        }
        .article-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .article-card .card-title {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .article-card .card-text {
            flex-grow: 1; /* Permet au texte de prendre l'espace disponible */
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; // Chemin vers votre navbar ?>

    <div class="container my-5">
        <h1 class="text-center mb-5">Tous nos Articles</h1>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if (empty($allArticles)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">Aucun article n'a encore été publié.</div>
                </div>
            <?php else: ?>
                <?php foreach ($allArticles as $article): ?>
                    <div class="col">
                        <div class="card article-card">
                            <img src="<?php echo htmlspecialchars($article['image_url'] ?: 'https://placehold.co/600x400/cccccc/333333?text=Article'); ?>" class="card-img-top" alt="Image de l'article">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(truncateText($article['content'], 150)); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <small class="text-muted">Par <?php echo htmlspecialchars($article['author_name']); ?></small>
                                    <a href="article.php?id=<?php echo htmlspecialchars($article['id']); ?>" class="btn btn-primary btn-sm">Lire la suite</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; // Chemin vers votre footer ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
