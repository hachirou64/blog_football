<?php
session_start(); // Toujours démarrer la session avant de la manipuler

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous voulez détruire complètement la session, effacez aussi le cookie de session.
// Note : Cela détruira la session, et pas seulement les données de session !
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalement, détruire la session.
session_destroy();

// Rediriger vers la page d'accueil
header('Location: /blog_football/');
exit();
?>