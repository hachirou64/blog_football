<?php
// pages/admin/add_edit_category.php

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
$category_data = [
    'id' => null,
    'name' => '',
];
$is_edit = false;

// --- CHARGER LES DONNÉES DE LA CATÉGORIE SI MODIFICATION ---
if (isset($_GET['id'])) {
    $category_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = :id");
        $stmt->execute(['id' => $category_id]);
        $fetched_category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fetched_category) {
            $category_data = $fetched_category;
            $is_edit = true;
        } else {
            $message = "<div class='alert alert-danger'>Catégorie non trouvée pour la modification.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur de base de données : " . $e->getMessage() . "</div>";
    }
}

// --- GESTION DE LA SOUMISSION DU FORMULAIRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');

    // Validation
    if (empty($name)) {
        $message = "<div class='alert alert-danger'>Veuillez saisir un nom pour la catégorie.</div>";
    } else {
        try {
            // Vérifier l'unicité du nom de la catégorie (sauf si c'est la catégorie actuelle et que le nom n'a pas changé)
            $sql_check = "SELECT id FROM categories WHERE name = :name";
            if ($is_edit) {
                $sql_check .= " AND id != :current_id";
            }
            $stmt_check = $pdo->prepare($sql_check);
            $check_params = ['name' => $name];
            if ($is_edit) {
                $check_params['current_id'] = $id;
            }
            $stmt_check->execute($check_params);

            if ($stmt_check->fetch()) {
                $message = "<div class='alert alert-danger'>Une catégorie avec ce nom existe déjà.</div>";
            } else {
                if ($is_edit) { // MISE À JOUR DE LA CATÉGORIE
                    $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
                    $stmt->execute([
                        'name' => $name,
                        'id' => $id
                    ]);
                    $message = "<div class='alert alert-success'>Catégorie mise à jour avec succès !</div>";
                    // Mettre à jour les données actuelles après une mise à jour réussie
                    $category_data['name'] = $name;
                } else { // AJOUT D'UNE NOUVELLE CATÉGORIE
                    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
                    $stmt->execute([
                        'name' => $name
                    ]);
                    $message = "<div class='alert alert-success'>Catégorie ajoutée avec succès !</div>";
                    // Réinitialiser le formulaire pour un nouvel ajout
                    $category_data = ['id' => null, 'name' => ''];
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
    <title><?php echo $is_edit ? 'Modifier' : 'Ajouter'; ?> une Catégorie - Admin</title>
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
                        <h3 class="text-center"><?php echo $is_edit ? 'Modifier' : 'Ajouter'; ?> une Catégorie</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>

                        <form action="add_edit_category.php<?php echo $is_edit ? '?id=' . htmlspecialchars($category_data['id']) : ''; ?>" method="POST">
                            <?php if ($is_edit): ?>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($category_data['id']); ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nom de la catégorie</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category_data['name']); ?>" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Mettre à jour la catégorie' : 'Ajouter la catégorie'; ?></button>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="manage_categories.php" class="btn btn-secondary">Retour à la gestion des catégories</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>