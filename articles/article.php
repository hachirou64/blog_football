<?php
echo "Bonjour depuis article.php ! L'ID reçu est : " . (isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'aucun');
?>
