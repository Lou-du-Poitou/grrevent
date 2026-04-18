<?php
    require_once './class/User.php';
    require_once './class/Logged.php';
    
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
