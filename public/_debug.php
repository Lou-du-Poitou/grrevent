<!-- 
/!\ Fichier à enlever de préférence pour le  
déploiement de l'application en production.
-->
<?php
    require_once __DIR__ . '/../config/constants.php';

    // Ne rien afficher en production
    if (PRODUCTION_ENV) die();

    require_once __DIR__ . '/../class/models/User.php';
    require_once __DIR__ . '/../class/utils/Logged.php';
    
    session_start();

    $logged = new Logged();
    $user = $logged->user();

    // phpinfo();
?>
<div>
    <?php 
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';

    if (isset($user)) {
        echo "<code>Id: {$user->getHTML('userId')}</code>;";
        echo '<br>';
        echo "<code>Pseudo: {$user->getHTML('userPseudo')}</code>;";
    }
    ?>
</div>
