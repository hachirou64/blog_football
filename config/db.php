<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_football'); // Assurez-vous que c'est le bon nom de votre base de données
define('DB_USER', 'root');         // Utilisateur XAMPP par défaut
define('DB_PASS', '');             // Mot de passe XAMPP par défaut (vide)

try {
    // Création de l'objet PDO pour la connexion à la base de données
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    // Définition du mode d'erreur de PDO à Exception pour une meilleure gestion des erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Définition du mode de récupération par défaut à FETCH_ASSOC pour des tableaux associatifs
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, arrêter le script et afficher le message d'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>