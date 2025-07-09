<?php
// pages/admin/add_edit_user.php

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
$user_data = [
    'id' => null,
    'username' => '',
    'email' => '',
    'role' => 'utilisateur' // Rôle par défaut pour un nouvel utilisateur
];
$is_edit = false; // Par défaut, nous sommes en mode ajout

// --- CHARGER LES DONNÉES DE L'UTILISATEUR SI C'EST UNE MODIFICATION ---
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $fetched_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fetched_user) {
            $user_data = $fetched_user;
            $is_edit = true;
        } else {
            $message = "<div class='alert alert-danger'>Utilisateur non trouvé pour la modification.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur de base de données : " . $e->getMessage() . "</div>";
    }
}

// --- GÉRER LA SOUMISSION DU FORMULAIRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // ID sera null pour un ajout, présent pour une modification
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'utilisateur';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Valider les champs
    if (empty($username) || empty($email) || empty($role)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs obligatoires (Nom d'utilisateur, Email, Rôle).</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Format d'email invalide.</div>";
    } else {
        try {
            // Vérifier l'unicité du nom d'utilisateur et de l'email (sauf si c'est l'utilisateur actuel et qu'il n'a pas changé)
            $sql_check = "SELECT id FROM users WHERE (username = :username OR email = :email)";
            if ($is_edit) {
                $sql_check .= " AND id != :current_id";
            }
            $stmt_check = $pdo->prepare($sql_check);
            $check_params = ['username' => $username, 'email' => $email];
            if ($is_edit) {
                $check_params['current_id'] = $id;
            }
            $stmt_check->execute($check_params);
            
            if ($stmt_check->fetch()) {
                $message = "<div class='alert alert-danger'>Le nom d'utilisateur ou l'email est déjà utilisé par un autre compte.</div>";
            } else {
                // Si tout est bon, procéder à l'insertion ou à la mise à jour
                if ($is_edit) { // Mode MODIFICATION
                    $update_fields = [
                        'username = :username',
                        'email = :email',
                        'role = :role'
                    ];
                    $params = [
                        'username' => $username,
                        'email' => $email,
                        'role' => $role,
                        'id' => $id
                    ];

                    // Si un nouveau mot de passe est fourni, le gérer
                    if (!empty($password)) {
                        if ($password !== $confirm_password) {
                            $message = "<div class='alert alert-danger'>Les mots de passe ne correspondent pas.</div>";
                        } else {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $update_fields[] = 'password = :password';
                            $params['password'] = $hashed_password;
                        }
                    }

                    if (empty($message)) {
                        $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = :id";
                        $stmt_update = $pdo->prepare($sql);
                        $stmt_update->execute($params);
                        $message = "<div class='alert alert-success'>Utilisateur mis à jour avec succès.</div>";
                         // Recharger les données pour afficher les changements immédiatement
                        $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = :id");
                        $stmt->execute(['id' => $id]);
                        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    }

                } else { // Mode AJOUT
                    if (empty($password) || empty($confirm_password)) {
                        $message = "<div class='alert alert-danger'>Veuillez définir un mot de passe pour le nouvel utilisateur.</div>";
                    } elseif ($password !== $confirm_password) {
                        $message = "<div class='alert alert-danger'>Les mots de passe ne correspondent pas.</div>";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
                        $stmt_insert = $pdo->prepare($sql);
                        $stmt_insert->execute([
                            'username' => $username,
                            'email' => $email,
                            'password' => $hashed_password,
                            'role' => $role
                        ]);
                        $message = "<div class='alert alert-success'>Utilisateur ajouté avec succès !</div>";
                        // Réinitialiser le formulaire pour un nouvel ajout
                        $user_data = ['id' => null, 'username' => '', 'email' => '', 'role' => 'utilisateur'];
                    }
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
    <title><?php echo $is_edit ? 'Modifier' : 'Ajouter'; ?> un Utilisateur - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center"><?php echo $is_edit ? 'Modifier' : 'Ajouter'; ?> un Utilisateur</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>

                        <form action="add_edit_user.php<?php echo $is_edit ? '?id=' . $user_data['id'] : ''; ?>" method="POST">
                            <?php if ($is_edit): ?>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_data['id']); ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Rôle</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="utilisateur" <?php echo ($user_data['role'] === 'utilisateur') ? 'selected' : ''; ?>>Utilisateur</option>
                                    <option value="admin" <?php echo ($user_data['role'] === 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                                </select>
                            </div>
                            
                            <hr>
                            <h4><?php echo $is_edit ? 'Changer le mot de passe (laisser vide si inchangé)' : 'Mot de passe (obligatoire pour un nouvel utilisateur)'; ?></h4>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" <?php echo $is_edit ? '' : 'required'; ?>>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" <?php echo $is_edit ? '' : 'required'; ?>>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Mettre à jour l\'utilisateur' : 'Ajouter l\'utilisateur'; ?></button>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="manage_users.php" class="btn btn-secondary">Retour à la liste des utilisateurs</a>
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