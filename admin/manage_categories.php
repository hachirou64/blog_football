<?php
// pages/admin/manage_categories.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; // Chemin corrigé

// --- VÉRIFICATION DE L'ACCÈS ADMINISTRATEUR ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /blog_football/login.php?error=access_denied');
    exit();
}

$message = '';
$categories = [];

// --- GESTION DE L'ACTION DE SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $category_id_to_delete = (int)$_GET['id'];

    try {
        // Optionnel : Vérifier si des articles sont liés à cette catégorie
        // Si oui, vous pouvez décider de :
        // 1. Empêcher la suppression si des articles sont liés (recommandé si category_id est NOT NULL dans 'posts')
        // 2. Mettre à jour category_id à NULL pour les articles liés (si category_id est NULLABLE dans 'posts')
        // J'ai mis ON DELETE SET NULL dans le SQL de 'posts', donc la 2ème option est gérée par la DB.
        // Si vous avez mis ON DELETE RESTRICT, il faudra faire un SELECT avant le DELETE.

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        if ($stmt->execute(['id' => $category_id_to_delete])) {
            $message = "<div class='alert alert-success'>Catégorie supprimée avec succès.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la suppression de la catégorie.</div>";
        }
    } catch (PDOException $e) {
        // Erreur 23000 est souvent une erreur de contrainte d'intégrité (clé étrangère)
        if ($e->getCode() === '23000') {
            $message = "<div class='alert alert-danger'>Impossible de supprimer cette catégorie car des articles lui sont associés. Veuillez d'abord modifier ou supprimer ces articles.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur de base de données : " . $e->getMessage() . "</div>";
        }
    }
}

// --- RÉCUPÉRER TOUTES LES CATÉGORIES ---
try {
    $stmt = $pdo->query("SELECT id, name, created_at FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Erreur lors du chargement des catégories : " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Catégories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Gérer les Catégories</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>

                        <div class="d-flex justify-content-end mb-3">
                            <a href="add_edit_category.php" class="btn btn-success">Ajouter une nouvelle catégorie</a>
                        </div>

                        <?php if (empty($categories)): ?>
                            <div class="alert alert-info">Aucune catégorie trouvée.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom de la catégorie</th>
                                            <th>Date de création</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($category['id']); ?></td>
                                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                <td><?php echo htmlspecialchars($category['created_at']); ?></td>
                                                <td>
                                                    <a href="add_edit_category.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                                                    <a href="manage_categories.php?action=delete&id=<?php echo $category['id']; ?>"
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">Supprimer</a>
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

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>