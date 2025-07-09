<?php
// pages/admin/add_edit_article.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; // Corrected path

// --- ADMIN ACCESS CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /blog_football/login.php?error=access_denied');
    exit();
}

$message = '';
$article_data = [
    'id' => null,
    'title' => '',
    'content' => '',
    'image_url' => '', // For current image if editing
    'category_id' => null, // If you have categories
];
$is_edit = false;

// --- LOAD ARTICLE DATA IF EDITING ---
if (isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT id, title, content, image_url, category_id FROM posts WHERE id = :id");
        $stmt->execute(['id' => $article_id]);
        $fetched_article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fetched_article) {
            $article_data = $fetched_article;
            $is_edit = true;
        } else {
            $message = "<div class='alert alert-danger'>Article non trouvé pour la modification.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur de base de données : " . $e->getMessage() . "</div>";
    }
}

// --- FETCH CATEGORIES (if you have a categories table) ---
$categories = [];
// Uncomment and adapt if you have a 'categories' table
/*
try {
    $stmt_cat = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error, but don't stop the page load
    $message .= "<div class='alert alert-warning'>Impossible de charger les catégories : " . $e->getMessage() . "</div>";
}
*/


// --- HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = $_POST['category_id'] ?? null; // Make sure this matches your DB type (INT or NULL)
    $current_image_url = $_POST['current_image_url'] ?? '';

    // Basic validation
    if (empty($title) || empty($content)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir le titre et le contenu de l'article.</div>";
    } else {
        try {
            $upload_dir = '../uploads/'; // Directory to save images. Make sure it exists and is writable!
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create if it doesn't exist
            }

            $image_file = $_FILES['image'] ?? null;
            $image_path = $current_image_url; // Default to current image if none uploaded

            if ($image_file && $image_file['error'] === UPLOAD_ERR_OK) {
                $file_name = uniqid('img_') . '.' . pathinfo($image_file['name'], PATHINFO_EXTENSION);
                $destination = $upload_dir . $file_name;

                if (move_uploaded_file($image_file['tmp_name'], $destination)) {
                    $image_path = '/blog_football/uploads/' . $file_name; // Path to save in DB
                    // Optional: Delete old image if it exists and is different from new one
                    // if ($is_edit && $current_image_url && $current_image_url !== $image_path) {
                    //    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $current_image_url)) {
                    //        unlink($_SERVER['DOCUMENT_ROOT'] . $current_image_url);
                    //    }
                    // }
                } else {
                    $message = "<div class='alert alert-danger'>Erreur lors du téléchargement de l'image.</div>";
                }
            } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
                // Remove existing image
                if ($current_image_url && file_exists($_SERVER['DOCUMENT_ROOT'] . $current_image_url)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $current_image_url);
                }
                $image_path = null; // Set to null in DB
            }


            if (empty($message)) { // Proceed only if no upload errors
                if ($is_edit) { // UPDATE ARTICLE
                    $stmt = $pdo->prepare("UPDATE posts SET title = :title, content = :content, image_url = :image_url, category_id = :category_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
                    $stmt->execute([
                        'title' => $title,
                        'content' => $content,
                        'image_url' => $image_path,
                        'category_id' => $category_id,
                        'id' => $id
                    ]);
                    $message = "<div class='alert alert-success'>Article mis à jour avec succès !</div>";
                    // Update current article data after successful update
                    $article_data['title'] = $title;
                    $article_data['content'] = $content;
                    $article_data['image_url'] = $image_path;
                    $article_data['category_id'] = $category_id;

                } else { // ADD NEW ARTICLE
                    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image_url, category_id) VALUES (:user_id, :title, :content, :image_url, :category_id)");
                    $stmt->execute([
                        'user_id' => $_SESSION['user_id'], // Current logged-in admin is the author
                        'title' => $title,
                        'content' => $content,
                        'image_url' => $image_path,
                        'category_id' => $category_id
                    ]);
                    $message = "<div class='alert alert-success'>Article ajouté avec succès !</div>";
                    // Clear form after adding
                    $article_data = ['id' => null, 'title' => '', 'content' => '', 'image_url' => '', 'category_id' => null];
                }
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur de base de données : " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Modifier' : 'Ajouter'; ?> un Article - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css"> </head>
<body>
    <?php include '../includes/navbar.php'; // Corrected path ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center"><?php echo $is_edit ? 'Modifier' : 'Ajouter'; ?> un Article</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>

                        <form action="add_edit_article.php<?php echo $is_edit ? '?id=' . htmlspecialchars($article_data['id']) : ''; ?>" method="POST" enctype="multipart/form-data">
                            <?php if ($is_edit): ?>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($article_data['id']); ?>">
                                <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($article_data['image_url']); ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="title" class="form-label">Titre de l'article</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($article_data['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu de l'article</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($article_data['content']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image de l'article (laisser vide pour ne pas changer)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <?php if ($is_edit && $article_data['image_url']): ?>
                                    <div class="mt-2">
                                        Image actuelle :<br>
                                        <img src="<?php echo htmlspecialchars($article_data['image_url']); ?>" alt="Image Article" style="max-width: 200px; height: auto;">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label" for="remove_image">Supprimer l'image actuelle</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Catégorie (optionnel)</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['id']); ?>"
                                            <?php echo ($article_data['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Mettre à jour l\'article' : 'Ajouter l\'article'; ?></button>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="manage_articles.php" class="btn btn-secondary">Retour à la gestion des articles</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; // Corrected path ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>