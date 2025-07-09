<?php
// Inclure la base de données n'est pas nécessaire ici pour la navbar,
// mais session_start() est crucial pour accéder à $_SESSION
// Assurez-vous que session_start() est appelé au début de TOUTES les pages
// qui incluent cette navbar et qui ont besoin d'accéder aux sessions.
// C'est déjà dans login.php et register.php, mais si vous l'incluez directement
// sur une page, assurez-vous qu'elle est appelée.

// Si la session n'est pas déjà démarrée, on la démarre (important pour éviter les erreurs)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/blog_football/">Mon Blog Foot</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" aria-current="page" href="/blog_football/">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/blog_football/#">Catégories</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): // Si l'utilisateur est connecté ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/blog_football/admin/create_article.php">Ajouter Article</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                            <?php if ($_SESSION['role'] === 'admin'): // Si l'utilisateur est admin ?>
                                <li><a class="dropdown-item" href="/blog_football/admin/dashboard.php">Tableau de bord Admin</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/blog_football/users/logout.php">Déconnexion</a></li>
                        </ul>
                    </li>
                <?php else: // Si l'utilisateur n'est PAS connecté ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/blog_football/users/login.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/blog_football/users/register.php">Inscription</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>