<?php
// Activer l'affichage des erreurs pour ce script de test
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Vérification de la constante XML_PI_NODE :<br>";

if (extension_loaded('dom')) {
    echo "L'extension DOM est chargée.<br>";
} else {
    echo "L'extension DOM N'EST PAS chargée.<br>";
    // Si elle n'est pas chargée ici, il y a un gros problème de cohérence
    // avec ce que phpinfo() affiche.
}

if (defined('XML_PI_NODE')) {
    echo "La constante XML_PI_NODE est définie.<br>";
    echo "Sa valeur est : " . XML_PI_NODE . "<br>";
} else {
    echo "ERREUR : La constante XML_PI_NODE N'EST PAS définie.<br>";
}

echo "<hr>Fin du test.";
?>
cd