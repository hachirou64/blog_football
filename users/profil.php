<?php
// pages/profile.php

// Démarrer la session pour accéder aux informations de l'utilisateur connecté
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données
// Le chemin est ../config/db.php car profile.php est dans pages/
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
    // Le chemin est /blog_football/login.php car login.php est à la racine du blog
    header('Location: /blog_football/login.php');
    exit();
}

$user = null;
$message = '';

// Récupérer l'ID de l'utilisateur depuis la session
$user_id = $_SESSION['user_id'];

try {
    // Récupérer les informations de l'utilisateur depuis la base de données
    // IMPORTANT : Inclure 'password_hash' dans la sélection pour la vérification du mot de passe
    $stmt = $pdo->prepare("SELECT id, username, email, password_hash, role FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Utiliser FETCH_ASSOC pour des clés de tableau associatives

    if (!$user) {
        // Si l'utilisateur n'est pas trouvé (ce qui ne devrait pas arriver si user_id est valide)
        $message = "<div class='alert alert-danger'>Erreur : Utilisateur non trouvé.</div>";
        // Optionnel : Déconnecter l'utilisateur si son ID n'est plus valide
        // session_destroy();
        // header('Location: /blog_football/login.php');
        // exit();
    }

} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Erreur lors du chargement du profil : " . $e->getMessage() . "</div>";
}


// Gérer la soumission du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    // Vérification des champs requis (si l'utilisateur veut les modifier)
    if (empty($new_username) || empty($new_email)) {
        $message = "<div class='alert alert-danger'>Le nom d'utilisateur et l'email ne peuvent pas être vides.</div>";
    } elseif ($new_email !== $user['email'] && !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
         $message = "<div class='alert alert-danger'>Format d'email invalide.</div>";
    } else {
        try {
            $update_fields = [];
            $params = ['id' => $user_id];

            // Vérifier si le nom d'utilisateur ou l'email sont déjà pris par quelqu'un d'autre
            // et que ce n'est pas l'utilisateur actuel
            if ($new_username !== $user['username']) {
                $stmt_check_username = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
                $stmt_check_username->execute(['username' => $new_username, 'id' => $user_id]);
                if ($stmt_check_username->fetch()) {
                    $message = "<div class='alert alert-danger'>Ce nom d'utilisateur est déjà pris.</div>";
                } else {
                    $update_fields[] = "username = :username";
                    $params['username'] = $new_username;
                }
            }

            if (empty($message) && $new_email !== $user['email']) {
                $stmt_check_email = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
                $stmt_check_email->execute(['email' => $new_email, 'id' => $user_id]);
                if ($stmt_check_email->fetch()) {
                    $message = "<div class='alert alert-danger'>Cet email est déjà utilisé.</div>";
                } else {
                    $update_fields[] = "email = :email";
                    $params['email'] = $new_email;
                }
            }
            
            // Si pas de message d'erreur et au moins un champ à mettre à jour
            if (empty($message) && (!empty($update_fields) || !empty($new_password))) {
                // Gestion du changement de mot de passe
                if (!empty($old_password) || !empty($new_password) || !empty($confirm_new_password)) {
                    // Utiliser $user['password_hash'] pour vérifier l'ancien mot de passe
                    if (!password_verify($old_password, $user['password_hash'])) {
                        $message = "<div class='alert alert-danger'>L'ancien mot de passe est incorrect.</div>";
                    } elseif (empty($new_password) || empty($confirm_new_password)) {
                        $message = "<div class='alert alert-danger'>Veuillez remplir les champs du nouveau mot de passe.</div>";
                    } elseif ($new_password !== $confirm_new_password) {
                        $message = "<div class='alert alert-danger'>Les nouveaux mots de passe ne correspondent pas.</div>";
                    } else {
                        // Hacher le nouveau mot de passe
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_fields[] = "password_hash = :password_hash"; // Mettre à jour la colonne password_hash
                        $params['password_hash'] = $hashed_password;
                    }
                }

                if (empty($message) && !empty($update_fields)) {
                    $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = :id";
                    $stmt_update = $pdo->prepare($sql);
                    $stmt_update->execute($params);

                    // Mettre à jour les informations de session si le nom d'utilisateur a changé
                    if (isset($params['username'])) {
                        $_SESSION['username'] = $params['username'];
                    }
                    
                    // Recharger les infos de l'utilisateur après la mise à jour pour refléter les changements
                    // IMPORTANT : Inclure 'password_hash' ici aussi pour les futures vérifications
                    $stmt = $pdo->prepare("SELECT id, username, email, password_hash, role FROM users WHERE id = :id");
                    $stmt->execute(['id' => $user_id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);


                    $message = "<div class='alert alert-success'>Profil mis à jour avec succès !</div>";
                } elseif (empty($message)) {
                    $message = "<div class='alert alert-info'>Aucune modification détectée.</div>";
                }
            }

        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur lors de la mise à jour du profil : " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css"> <!-- Chemin vers votre CSS -->
</head>
<body>
    <?php include '../includes/navbar.php'; // Chemin vers votre navbar ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Mon Profil</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>

                        <?php if ($user): ?>
                            <form action="profile.php" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rôle</label>
                                    <p class="form-control-static"><?php echo htmlspecialchars($user['role']); ?></p>
                                </div>

                                <hr>

                                <h4>Changer de mot de passe (laisser vide si pas de changement)</h4>
                                <div class="mb-3">
                                    <label for="old_password" class="form-label">Ancien mot de passe</label>
                                    <input type="password" class="form-control" id="old_password" name="old_password">
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_new_password" class="form-label">Confirmer nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <p class="text-center">Impossible de charger les informations du profil.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; // Chemin vers votre footer ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
