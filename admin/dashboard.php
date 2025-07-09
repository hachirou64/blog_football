<?php
// Démarrer la session pour accéder aux informations de l'utilisateur
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Protection : Vérifier si l'utilisateur est connecté et s'il a le rôle "admin"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirection vers la page de connexion, le chemin corrigé doit être ../login.php ou /blog_football/login.php
    header('Location: /blog_football/login.php?error=access_denied'); // OU header('Location: ../login.php?error=access_denied');
    exit();
}

require_once '../config/db.php'; // Pour la connexion à la base de données

// --- Récupération des statistiques pour l'admin ---
$total_articles = 0;
$total_users = 0;
$total_categories = 0; // Nouvelle variable pour les catégories

try {
    // Total des articles (si votre table s'appelle 'posts', ajustez ici)
    $stmt = $pdo->query("SELECT COUNT(*) FROM posts"); // Assurez-vous que c'est 'posts' ou 'articles' selon votre table
    $total_articles = $stmt->fetchColumn();

    // Total des utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $stmt->fetchColumn();

    // Total des catégories
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $total_categories = $stmt->fetchColumn();

} catch (PDOException $e) {
    // Gérer l'erreur si besoin
    $error_message = "Erreur lors de la récupération des statistiques: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tableau de Bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h1 class="mb-4">Tableau de Bord Administrateur</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Articles</h5>
                        <p class="card-text fs-1"><?php echo $total_articles; ?></p>
                        <a href="manage_articles.php" class="btn btn-light btn-sm">Gérer les articles</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Utilisateurs</h5>
                        <p class="card-text fs-1"><?php echo $total_users; ?></p>
                        <a href="manage_users.php" class="btn btn-light">Gérer les utilisateurs</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Catégories</h5>
                        <p class="card-text fs-1"><?php echo $total_categories; ?></p> <a href="manage_categories.php" class="btn btn-light btn-sm">Gérer les catégories</a>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <h3>Actions Rapides</h3>
        <div class="list-group">
            <a href="add_edit_article.php" class="list-group-item list-group-item-action">Créer un nouvel article</a> <a href="manage_articles.php" class="list-group-item list-group-item-action">Voir tous les articles</a>
            <a href="add_edit_category.php" class="list-group-item list-group-item-action">Créer une nouvelle catégorie</a> <a href="manage_categories.php" class="list-group-item list-group-item-action">Voir toutes les catégories</a> <a href="add_edit_user.php" class="list-group-item list-group-item-action">Ajouter un nouvel utilisateur</a>
            <a href="manage_users.php" class="list-group-item list-group-item-action">Voir tous les utilisateurs</a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>