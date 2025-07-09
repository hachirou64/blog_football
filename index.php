<?php
// index.php - Page d'accueil du blog

// Démarrer la session pour accéder aux informations de l'utilisateur (si connecté)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données
// Le chemin est 'config/db.php' car index.php est à la racine de blog_football/
require_once 'config/db.php';

$recentArticles = [];
$categories = [];
$errorMessage = '';

try {
    // Récupérer les 3 derniers articles pour la section "Articles Récents"
    // Assurez-vous que votre table d'articles s'appelle 'posts'
    $stmtArticles = $pdo->query("SELECT p.id, p.title, p.content, p.image_url, p.created_at, u.username AS author_name
                                 FROM posts p
                                 JOIN users u ON p.user_id = u.id
                                 ORDER BY p.created_at DESC
                                 LIMIT 3");
    $recentArticles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer toutes les catégories
    $stmtCategories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorMessage = "Erreur lors du chargement des données : " . $e->getMessage();
}

// Fonction utilitaire pour tronquer le texte
function truncateText($text, $maxLength = 150) {
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
    <title>Blog de Football - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"> <!-- Chemin vers votre CSS personnalisé -->
    <!-- Optionnel: Font Awesome pour des icônes si vous en utilisez -->
    <!-- <script src="https://kit.fontawesome.com/your_font_awesome_kit_code.js" crossorigin="anonymous"></script> -->
    <style>
        /* Styles personnalisés pour améliorer l'esthétique */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://placehold.co/1920x600/000000/FFFFFF?text=Fond+de+Football') no-repeat center center; /* Remplacez par une vraie image de fond si vous en avez une */
            background-size: cover;
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }
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
            height: 200px; /* Hauteur fixe pour les images des articles */
            object-fit: cover;
            width: 100%;
        }
        .article-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .article-card .card-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .article-card .card-text {
            flex-grow: 1; /* Permet au texte de prendre l'espace disponible */
            margin-bottom: 15px;
        }
        .category-badge {
            display: inline-block;
            background-color: #0d6efd; /* Couleur Bootstrap Primary */
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            margin: 5px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .category-badge:hover {
            background-color: #0a58ca; /* Couleur Bootstrap Primary Darker */
            color: white;
        }
        .cta-section {
            background-color: #f8f9fa; /* Couleur Bootstrap Light */
            padding: 60px 0;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Section Héro -->
    <section class="hero-section">
        <div class="container">
            <h1>Bienvenue sur le Blog de Football</h1>
            <p>Toute l'actualité, les analyses et les débats sur le monde du ballon rond. De la Ligue 1 à la Ligue des Champions, ne manquez rien !</p>
            <a href="#recent-articles" class="btn btn-light btn-lg">Découvrir les articles</a>
        </div>
    </section>

    <main class="container my-5">
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Section Articles Récents -->
        <section id="recent-articles" class="mb-5">
            <h2 class="text-center mb-4">Derniers Articles</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php if (empty($recentArticles)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">Aucun article n'a encore été publié.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentArticles as $article): ?>
                        <div class="col">
                            <div class="card article-card">
                                <img src="<?php echo htmlspecialchars($article['image_url'] ?: 'https://placehold.co/600x400/cccccc/333333?text=Article'); ?>" class="card-img-top" alt="Image de l'article">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(truncateText($article['content'], 120)); ?></p>
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
            <div class="text-center mt-4">
                <a href="articles.php" class="btn btn-outline-primary">Voir tous les articles</a>
            </div>
        </section>

        <!-- Section Catégories -->
        <section id="categories" class="mb-5">
            <h2 class="text-center mb-4">Explorer par Catégorie</h2>
            <div class="text-center">
                <?php if (empty($categories)): ?>
                    <div class="alert alert-info">Aucune catégorie n'a encore été créée.</div>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <a href="category.php?id=<?php echo htmlspecialchars($category['id']); ?>" class="category-badge">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section Appel à l'action / Newsletter -->
        <section class="cta-section">
            <div class="container">
                <h2>Restez connecté au monde du Football !</h2>
                <p class="lead">Abonnez-vous à notre newsletter pour recevoir les dernières actualités et analyses directement dans votre boîte mail.</p>
                <!-- Formulaire de newsletter simplifié (vous devrez implémenter la logique d'envoi) -->
                <form class="row justify-content-center g-3">
                    <div class="col-md-5">
                        <input type="email" class="form-control form-control-lg" placeholder="Votre adresse email" required>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary btn-lg">S'abonner</button>
                    </div>
                </form>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
