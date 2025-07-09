<?php
// register.php

// Démarrer la session au tout début du script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données
// Le chemin est 'config/db.php' si register.php est à la racine de blog_football/
require_once '../config/db.php';

$message = '';

// Si l'utilisateur est déjà connecté, redirigez-lo vers l'accueil.
if (isset($_SESSION['user_id'])) {
    header('Location: /blog_football/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Format d'email invalide.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Les mots de passe ne correspondent pas.</div>";
    } else {
        try {
            // Vérifier si le nom d'utilisateur ou l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            if ($stmt->fetch()) {
                $message = "<div class='alert alert-danger'>Ce nom d'utilisateur ou cet email est déjà utilisé.</div>";
            } else {
                // Hacher le mot de passe avant de l'enregistrer
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insérer le nouvel utilisateur dans la base de données
                // Assurez-vous que le nombre de colonnes dans VALUES correspond au nombre de paramètres dans execute
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)");
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $hashed_password,
                    'role' => 'utilisateur' // Rôle par défaut
                ]);

                $message = "<div class='alert alert-success'>Inscription réussie ! Vous pouvez maintenant vous <a href='login.php'>connecter</a>.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur lors de l'inscription : " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"> </head>
<body>
   

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Inscription</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">S'inscrire</button>
                            </div>
                            <p class="mt-3 text-center">
                                Déjà un compte ? <a href="login.php">Connectez-vous ici</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
