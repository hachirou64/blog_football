<?php
// Démarrer la session pour accéder aux informations de l'utilisateur
// Cela doit être la TOUTE PREMIÈRE CHOSE dans le script, avant tout HTML
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; // Chemin correct pour db.php (remonte d'un dossier)

// --- DEBUT DE LA SECTION DE PROTECTION ---
// Vérifier si l'utilisateur est connecté ET s'il a le rôle "admin"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Rediriger vers la page de connexion avec un message d'erreur d'accès refusé
    header('Location: /blog_football/users/login.php?error=access_denied');
    exit(); // Très important d'arrêter l'exécution du script après une redirection
}
// --- FIN DE LA SECTION DE PROTECTION ---


$message = '';
$categories = [];

// Récupérer les catégories existantes pour le formulaire
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Erreur lors du chargement des catégories : " . $e->getMessage();
}


// Gérer l'envoi du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    // L'ID de l'auteur est maintenant l'ID de l'utilisateur connecté via la session
    $author_id = $_SESSION['user_id']; 

    if (empty($title) || empty($content) || empty($category_id)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs requis (Titre, Contenu, Catégorie).</div>";
    } else {
        try {
            // Insérer l'article dans la base de données
            $stmt = $pdo->prepare("INSERT INTO articles (title, content, author_id, category_id) VALUES (:title, :content, :author_id, :category_id)");
            $stmt->execute([
                'title' => $title,
                'content' => $content,
                'author_id' => $author_id,
                'category_id' => $category_id
            ]);

            $message = "<div class='alert alert-success'>Article ajouté avec succès !</div>";
            // Optionnel : Vider les champs après succès
            $title = '';
            $content = '';

        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur lors de l'ajout de l'article : " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ajouter un Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; // Inclure la navbar ?>

    <div class="container mt-4">
        <h1>Ajouter un Nouvel Article</h1>
        <p><?php echo $message; ?></p>

        <form action="create_article.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Titre de l'Article</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Contenu de l'Article</label>
                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Catégorie</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Sélectionnez une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter l'Article</button>
        </form>
    </div>

    <?php include '../includes/footer.php'; // Inclure le footer ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>