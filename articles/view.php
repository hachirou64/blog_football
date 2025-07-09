<?php
// Démarrer la session au tout début du script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; // Chemin correct pour db.php

$article = null;
$message = '';
$comments = []; // Nouvelle variable pour stocker les commentaires

// Récupérer l'ID de l'article depuis l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $article_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Préparer et exécuter la requête pour récupérer l'article
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.content, a.created_at, u.username AS author_name, c.name AS category_name
                               FROM articles a
                               JOIN users u ON a.author_id = u.id
                               JOIN categories c ON a.category_id = c.id
                               WHERE a.id = :id");
        $stmt->execute(['id' => $article_id]);
        $article = $stmt->fetch();

        if (!$article) {
            $message = "<div class='alert alert-danger'>Article non trouvé.</div>";
        } else {
            // Incrémenter le compteur de vues (optionnel, peut être fait différemment pour plus de précision)
            $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id")->execute(['id' => $article_id]);

            // --- NOUVEAU : Récupérer les commentaires pour cet article ---
            $stmt_comments = $pdo->prepare("SELECT co.content, co.created_at, u.username AS comment_author
                                            FROM comments co
                                            JOIN users u ON co.user_id = u.id
                                            WHERE co.article_id = :article_id
                                            ORDER BY co.created_at DESC");
            $stmt_comments->execute(['article_id' => $article_id]);
            $comments = $stmt_comments->fetchAll();
        }

    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur lors du chargement de l'article : " . $e->getMessage() . "</div>";
    }
} else {
    $message = "<div class='alert alert-warning'>Aucun ID d'article spécifié.</div>";
}

// --- NOUVEAU : Gérer l'ajout de commentaire (si un utilisateur est connecté) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    if (isset($_SESSION['user_id'])) { // Vérifie si l'utilisateur est connecté
        $comment_content = trim($_POST['comment_content'] ?? '');
        $user_id = $_SESSION['user_id'];

        if (empty($comment_content)) {
            $message = "<div class='alert alert-danger'>Veuillez écrire un commentaire.</div>";
        } else {
            try {
                $stmt_insert_comment = $pdo->prepare("INSERT INTO comments (article_id, user_id, content) VALUES (:article_id, :user_id, :content)");
                $stmt_insert_comment->execute([
                    'article_id' => $article_id,
                    'user_id' => $user_id,
                    'content' => $comment_content
                ]);
                $message = "<div class='alert alert-success'>Votre commentaire a été ajouté avec succès !</div>";
                // Recharger les commentaires pour afficher le nouveau
                header("Location: view.php?id=" . $article_id); // Redirection pour éviter le re-post du formulaire
                exit();
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>Erreur lors de l'ajout du commentaire : " . $e->getMessage() . "</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>Vous devez être connecté pour laisser un commentaire.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article['title']) : 'Article'; ?> - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <?php echo $message; ?>

        <?php if ($article): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <p class="card-subtitle mb-2 text-muted">
                        Par <?php echo htmlspecialchars($article['author_name']); ?>
                        le <?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?>
                        dans la catégorie <span class="badge bg-secondary"><?php echo htmlspecialchars($article['category_name']); ?></span>
                    </p>
                    <hr>
                    <div class="card-text">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h3>Commentaires (<?php echo count($comments); ?>)</h3>
                <hr>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            Laisser un commentaire
                        </div>
                        <div class="card-body">
                            <form action="view.php?id=<?php echo $article['id']; ?>" method="POST">
                                <div class="mb-3">
                                    <textarea class="form-control" name="comment_content" rows="3" placeholder="Votre commentaire..." required></textarea>
                                </div>
                                <button type="submit" name="add_comment" class="btn btn-primary">Poster le commentaire</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="alert alert-info text-center">
                        <a href="/blog_football/users/login.php">Connectez-vous</a> pour laisser un commentaire.
                    </p>
                <?php endif; ?>

                <?php if (empty($comments)): ?>
                    <p>Aucun commentaire pour le moment. Soyez le premier !</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <strong><?php echo htmlspecialchars($comment['comment_author']); ?></strong>
                                    le <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                </h6>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>