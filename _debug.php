<?php
    require_once './class/User.php';
    session_start();
?>
<div>
    <?php 
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';

    if (isset($_SESSION['user'])) {
        echo "<code>Pseudo: {$_SESSION['user']->getHTML('userPseudo')}</code>;";
    }
    ?>
</div>
