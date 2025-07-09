<?php
// pages/admin/manage_users.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

// --- VÉRIFICATION DE L'ACCÈS ADMINISTRATEUR ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /blog_football/pages/login.php?error=access_denied');
    exit();
}

$message = '';
$users = []; // Pour stocker la liste des utilisateurs

// --- GESTION DES ACTIONS (AJOUTER/MODIFIER/SUPPRIMER) ---

// Suppression d'un utilisateur
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id_to_delete = (int)$_GET['id'];

    // Empêcher un admin de se supprimer lui-même
    if ($user_id_to_delete === $_SESSION['user_id']) {
        $message = "<div class='alert alert-danger'>Vous ne pouvez pas supprimer votre propre compte d'administrateur.</div>";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            if ($stmt->execute(['id' => $user_id_to_delete])) {
                $message = "<div class='alert alert-success'>Utilisateur supprimé avec succès.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors de la suppression de l'utilisateur.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur de base de données : " . $e->getMessage() . "</div>";
        }
    }
}

// --- AFFICHAGE DE LA LISTE DES UTILISATEURS ---
try {
    $stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY username ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Erreur lors du chargement des utilisateurs : " . $e->getMessage() . "</div>";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Utilisateurs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Gérer les Utilisateurs</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>

                        <div class="d-flex justify-content-end mb-3">
                            <a href="add_edit_user.php" class="btn btn-success">Ajouter un nouvel utilisateur</a>
                        </div>

                        <?php if (empty($users)): ?>
                            <div class="alert alert-info">Aucun utilisateur trouvé.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom d'utilisateur</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user_item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user_item['id']); ?></td>
                                                <td><?php echo htmlspecialchars($user_item['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user_item['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user_item['role']); ?></td>
                                                <td>
                                                    <a href="add_edit_user.php?id=<?php echo $user_item['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                                                    <?php if ($user_item['id'] !== $_SESSION['user_id']): // Empêcher la suppression de son propre compte ?>
                                                        <a href="manage_users.php?action=delete&id=<?php echo $user_item['id']; ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-secondary" disabled title="Vous ne pouvez pas supprimer votre propre compte">Supprimer</button>
                                                    <?php endif; ?>
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