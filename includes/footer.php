<?php
// includes/footer.php

// Récupérer l'année actuelle pour le copyright
$currentYear = date('Y');
?>

<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row text-center text-md-start">

            <!-- Section du Blog -->
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary">Blog de Football</h5>
                <p>Votre source d'informations incontournable pour toute l'actualité, les analyses et les débats passionnants du monde du football.</p>
            </div>

            <!-- Liens Utiles -->
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary">Liens Utiles</h5>
                <p>
                    <a href="/blog_football/index.php" class="text-white" style="text-decoration: none;">Accueil</a>
                </p>
                <p>
                    <a href="/blog_football/articles.php" class="text-white" style="text-decoration: none;">Articles</a>
                </p>
                <p>
                    <a href="/blog_football/contact.php" class="text-white" style="text-decoration: none;">Contact</a>
                </p>
                <p>
                    <a href="/blog_football/about.php" class="text-white" style="text-decoration: none;">À propos</a>
                </p>
            </div>

            <!-- Contact -->
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary">Contact</h5>
                <p>
                    <i class="fas fa-home me-3"></i> Godomey, Atlantique, Bénin
                </p>
                <p>
                    <i class="fas fa-envelope me-3"></i> contact@blogfootball.com
                </p>
                <p>
                    <i class="fas fa-phone me-3"></i> +229 97 00 00 00
                </p>
            </div>

            <!-- Suivez-nous -->
            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary">Suivez-nous</h5>
                <a href="#" class="btn btn-outline-light btn-floating m-1" role="button">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="btn btn-outline-light btn-floating m-1" role="button">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="btn btn-outline-light btn-floating m-1" role="button">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="btn btn-outline-light btn-floating m-1" role="button">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <hr class="mb-4">

        <!-- Copyright -->
        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8">
                <p class="text-center text-md-start">
                    © <?php echo $currentYear; ?> Blog de Football. Tous droits réservés.
                </p>
            </div>
            <div class="col-md-5 col-lg-4">
                <div class="text-center text-md-end">
                    <!-- Liens de politique de confidentialité, etc. -->
                    <a href="/blog_football/pages/privacy.php" class="text-white me-3" style="text-decoration: none;">Politique de Confidentialité</a>
                    <a href="/blog_football/pages/terms.php" class="text-white" style="text-decoration: none;">Conditions d'Utilisation</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Font Awesome pour les icônes (si non déjà inclus dans la navbar ou le head) -->
<!-- Assurez-vous d'avoir votre propre kit Font Awesome ou utilisez la version CDN gratuite -->
<script src="https://kit.fontawesome.com/your_font_awesome_kit_code.js" crossorigin="anonymous"></script>
