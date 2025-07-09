<?php
// pages/contact.php - Page de contact

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données (peut être utile pour de futures extensions)
// Le chemin est ../config/db.php car contact.php est dans pages/
require_once 'config/db.php';

$message = ''; // Pour afficher les messages de succès ou d'erreur
$name = '';
$email = '';
$subject = '';
$user_message = ''; // Renommé pour éviter le conflit avec la variable $message principale

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $user_message = trim($_POST['user_message'] ?? '');

    // Validation des champs
    if (empty($name) || empty($email) || empty($subject) || empty($user_message)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs du formulaire.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Veuillez saisir une adresse email valide.</div>";
    } else {
        // --- Logique d'envoi d'email ---
        // ATTENTION : Pour que l'envoi d'email fonctionne réellement,
        // votre serveur XAMPP doit être configuré pour envoyer des mails (via sendmail ou SMTP).
        // Par défaut, XAMPP n'envoie pas d'emails sans configuration supplémentaire.
        // Si vous testez en local, le mail ne sera probablement pas envoyé sans cette configuration.
        // Pour un site en production, vous utiliseriez un service SMTP (SendGrid, Mailgun, etc.)

        $to = 'votre_email@example.com'; // REMPLACEZ PAR VOTRE VRAIE ADRESSE EMAIL
        $email_subject = "Message du Blog Football - " . htmlspecialchars($subject);
        $email_body = "Nom: " . htmlspecialchars($name) . "\n";
        $email_body .= "Email: " . htmlspecialchars($email) . "\n\n";
        $email_body .= "Message:\n" . htmlspecialchars($user_message);
        $headers = "From: " . htmlspecialchars($email) . "\r\n";
        $headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
        $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

        if (mail($to, $email_subject, $email_body, $headers)) {
            $message = "<div class='alert alert-success'>Votre message a été envoyé avec succès ! Nous vous répondrons bientôt.</div>";
            // Réinitialiser les champs du formulaire après envoi réussi
            $name = '';
            $email = '';
            $subject = '';
            $user_message = '';
        } else {
            $message = "<div class='alert alert-danger'>Désolé, une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer plus tard.</div>";
            // Pour le débogage local, vous pourriez vouloir logguer l'erreur mail()
            // error_log("Erreur d'envoi de mail : " . error_get_last()['message']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css"> <!-- Chemin vers votre CSS personnalisé -->
    <!-- Optionnel: Font Awesome pour des icônes si vous en utilisez -->
    <!-- <script src="https://kit.fontawesome.com/your_font_awesome_kit_code.js" crossorigin="anonymous"></script> -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Chemin vers votre navbar ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="text-center mb-4">Contactez-nous</h1>
                <p class="lead text-center mb-5">
                    Nous sommes toujours ravis d'entendre nos lecteurs ! Utilisez le formulaire ci-dessous pour nous envoyer un message.
                </p>

                <?php echo $message; // Afficher les messages de succès ou d'erreur ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="contact.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Votre Nom</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Votre Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Sujet</label>
                                <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_message" class="form-label">Votre Message</label>
                                <textarea class="form-control" id="user_message" name="user_message" rows="5" required><?php echo htmlspecialchars($user_message); ?></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Envoyer le message</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <h3>Informations de Contact Directes</h3>
                    <p>
                        Si vous préférez, vous pouvez nous contacter directement :
                    </p>
                    <p class="lead">
                        <i class="fas fa-envelope me-2"></i> Email: <a href="mailto:contact@blogfootball.com">contact@blogfootball.com</a>
                    </p>
                    <p class="lead">
                        <i class="fas fa-phone me-2"></i> Téléphone: +229 97 00 00 00
                    </p>
                    <p class="lead">
                        <i class="fas fa-map-marker-alt me-2"></i> Adresse: Godomey, Atlantique, Bénin
                    </p>
                </div>

            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Font Awesome pour les icônes (si non déjà inclus dans le head ou la navbar) -->
    <!-- <script src="https://kit.fontawesome.com/your_font_awesome_kit_code.js" crossorigin="anonymous"></script> -->
</body>
</html>
