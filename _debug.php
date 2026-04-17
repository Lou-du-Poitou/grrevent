<?php
    require_once './class/User.php';
    session_start();

    // phpinfo();
?>
<div>
    <?php 
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';

    if (isset($_SESSION['user'])) {
        echo "<code>Id: {$_SESSION['user']->getHTML('userId')}</code>;";
        echo '<br>';
        echo "<code>Pseudo: {$_SESSION['user']->getHTML('userPseudo')}</code>;";
    }
    ?>
</div>
