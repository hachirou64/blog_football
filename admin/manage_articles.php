<?php
// pages/admin/manage_articles.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; // Corrected path based on previous conversation

// --- ADMIN ACCESS CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /blog_football/login.php?error=access_denied');
    exit();
}

$message = '';
$articles = [];

// --- HANDLE DELETE ACTION ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $article_id_to_delete = (int)$_GET['id'];

    try {
        // Optional: Get image_url to delete the file from the server
        // $stmt_img = $pdo->prepare("SELECT image_url FROM posts WHERE id = :id");
        // $stmt_img->execute(['id' => $article_id_to_delete]);
        // $image_to_delete = $stmt_img->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
        if ($stmt->execute(['id' => $article_id_to_delete])) {
            $message = "<div class='alert alert-success'>Article supprimé avec succès.</div>";
            // Optional: Delete the actual image file
            // if ($image_to_delete && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_to_delete)) {
            //     unlink($_SERVER['DOCUMENT_ROOT'] . $image_to_delete);
            // }
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la suppression de l'article.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur de base de données : " . $e->getMessage() . "</div>";
    }
}

// --- FETCH ALL ARTICLES ---
try {
    // Join with users table to display author's username
    $stmt = $pdo->query("SELECT p.id, p.title, p.created_at, u.username AS author FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Erreur lors du chargement des articles : " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Articles - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css"> </head>
<body>
    <?php include '../includes/navbar.php'; // Corrected path ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Gérer les Articles</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>

                        <div class="d-flex justify-content-end mb-3">
                            <a href="add_edit_article.php" class="btn btn-success">Ajouter un nouvel article</a>
                        </div>

                        <?php if (empty($articles)): ?>
                            <div class="alert alert-info">Aucun article trouvé.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Titre</th>
                                            <th>Auteur</th>
                                            <th>Date de création</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($articles as $article): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($article['id']); ?></td>
                                                <td><?php echo htmlspecialchars($article['title']); ?></td>
                                                <td><?php echo htmlspecialchars($article['author']); ?></td>
                                                <td><?php echo htmlspecialchars($article['created_at']); ?></td>
                                                <td>
                                                    <a href="add_edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                                                    <a href="manage_articles.php?action=delete&id=<?php echo $article['id']; ?>"
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">Supprimer</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; // Corrected path ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>