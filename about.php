<?php
// pages/about.php - Page "À propos"

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données (peut être utile pour de futures extensions)
// Le chemin est ../config/db.php car about.php est dans pages/
require_once 'config/db.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Blog de Football</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css"> <!-- Chemin vers votre CSS personnalisé -->
    <!-- Optionnel: Font Awesome pour des icônes si vous en utilisez -->
    <!-- <script src="https://kit.fontawesome.com/your_font_awesome_kit_code.js" crossorigin="anonymous"></script> -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Chemin vers votre navbar ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="text-center mb-4">À propos de notre Blog de Football</h1>

                <p class="lead text-center mb-5">
                    Bienvenue sur le Blog de Football, votre destination ultime pour tout ce qui concerne le sport le plus populaire au monde !
                </p>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <img src="https://placehold.co/600x400/cccccc/333333?text=Notre+Mission" class="img-fluid rounded shadow-sm" alt="Image de Notre Mission">
                    </div>
                    <div class="col-md-6 mb-4">
                        <h3>Notre Mission</h3>
                        <p>
                            Notre mission est de fournir aux passionnés de football des informations de qualité, des analyses approfondies et des débats stimulants. Que vous soyez un supporter inconditionnel, un joueur amateur ou un simple curieux, nous nous engageons à vous offrir un contenu riche et pertinent sur l'actualité des championnats, les tactiques de jeu, les transferts, les profils de joueurs et l'histoire du football.
                        </p>
                        <p>
                            Nous croyons que le football est plus qu'un simple jeu ; c'est une culture, une passion qui unit des millions de personnes à travers le globe. C'est pourquoi nous nous efforçons de capturer cette essence dans chacun de nos articles.
                        </p>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-6 order-md-2 mb-4">
                        <img src="https://placehold.co/600x400/cccccc/333333?text=Notre+%C3%89quipe" class="img-fluid rounded shadow-sm" alt="Image de Notre Équipe">
                    </div>
                    <div class="col-md-6 order-md-1 mb-4">
                        <h3>Notre Équipe</h3>
                        <p>
                            Notre équipe est composée d'experts et de passionnés de football, chacun apportant sa propre perspective et son expertise. Des rédacteurs aux analystes, nous partageons tous le même amour pour le beau jeu. Nous travaillons sans relâche pour vous apporter les informations les plus récentes et les plus précises, ainsi que des opinions bien fondées qui susciteront la réflexion.
                        </p>
                        <p>
                            Nous sommes également une communauté ! Nous encourageons nos lecteurs à interagir, à commenter nos articles et à partager leurs propres points de vue. Votre passion est notre moteur.
                        </p>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <h3>Rejoignez notre communauté !</h3>
                    <p>
                        Suivez-nous sur les réseaux sociaux et abonnez-vous à notre newsletter pour ne rien manquer de l'actualité du football.
                    </p>
                    <a href="/blog_football/pages/register.php" class="btn btn-primary btn-lg">S'inscrire maintenant</a>
                </div>

            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; // Chemin vers votre footer ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
