<?php
// pages/login.php (ou à la racine, selon votre structure)

// Démarrer la session PHP au tout début du script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; // Chemin corrigé si login.php est à la racine

$message = '';

// Si l'utilisateur est déjà connecté, redirigez-le vers l'accueil.
if (isset($_SESSION['user_id'])) {
    header('Location: /blog_football/index.php');
    exit();
}

// Gérer les messages d'erreur de redirection (comme 'access_denied')
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'access_denied') {
        $message = "<div class='alert alert-danger'>Accès refusé. Vous devez être connecté en tant qu'administrateur pour accéder à cette page.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_username = trim($_POST['email_or_username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email_or_username) || empty($password)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs.</div>";
    } else {
        try {
            // Vérifier si l'entrée est un email ou un nom d'utilisateur
            // ASSUREZ-VOUS QUE LA COLONNE EST BIEN 'password_hash'
            if (filter_var($email_or_username, FILTER_VALIDATE_EMAIL)) {
                $stmt = $pdo->prepare("SELECT id, username, email, password_hash, role FROM users WHERE email = :email_or_username");
            } else {
                $stmt = $pdo->prepare("SELECT id, username, email, password_hash, role FROM users WHERE username = :email_or_username");
            }
            $stmt->execute(['email_or_username' => $email_or_username]);
            $user = $stmt->fetch();

            // UTILISER 'password_hash' POUR LA VÉRIFICATION
            if ($user && password_verify($password, $user['password_hash'])) {
                // Mot de passe correct, démarrer la session et stocker les infos utilisateur
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // Stocker le rôle de l'utilisateur

                // Rediriger vers la page d'accueil après une connexion réussie
                header('Location: /blog_football/index.php');
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Nom d'utilisateur/Email ou mot de passe incorrect.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur lors de la connexion : " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"> <!-- Chemin corrigé si login.php est à la racine -->
</head>
<body>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Connexion</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="email_or_username" class="form-label">Nom d'utilisateur ou Email</label>
                                <input type="text" class="form-control" id="email_or_username" name="email_or_username" value="<?php echo htmlspecialchars($email_or_username ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Se connecter</button>
                            </div>
                            <p class="mt-3 text-center">
                                Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>