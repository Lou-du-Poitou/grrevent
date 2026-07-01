<?php
require_once './config/connection.php';
require_once './config/constants.php';

require_once './elements/inputs.php';
require_once './elements/cards.php';

require_once './actions/user.actions.php';
require_once './actions/event.actions.php';

require_once './class/utils/Logged.php';
require_once './class/utils/HostUrl.php';

// Paramètres passés au header
$titlePage = 'Accueil';
$metaDescription = "Page d'accueil de l'application. Gérez et publiez vos événements en toute simplicité avec " . 
    SITE_NAME . " !";

$logged = new Logged();

$offset = 0;
if (isset($_GET['offset'])) {
    $offset = (int)$_GET['offset'];
}

if ($offset >= 0) {
    if ($logged->is()) {
        $db = connection();

        $userId = $logged->user()->getValue('userId');

        $addedEvent = selectAddedEvents($db, 
            $userId,
            DEFAULT_SELECT_LIMIT,
            $offset
        );

        $followedEvents = selectFollowedEvents($db,
            $userId,
            DEFAULT_SELECT_LIMIT,
            $offset
        );

        $db = null;
    };
}

require './elements/header.php';
?>
<?php if ($logged->is()): ?>
<div class="container">
    <?php if (isset($addedEvent) && isset($followedEvents) && isset($offset)): ?>
    <h1 class="part-title">Événements ajoutés</h1>
    <?= cardsThread($addedEvent, $_SERVER['SCRIPT_NAME'], $offset, false) ?>

    <h1 class="part-title">Événements des suivis</h1>
    <?= cardsThread($followedEvents, $_SERVER['SCRIPT_NAME'], $offset) ?>

    <?php endif ?>
</div>

<?php else: ?>
<div class="alert-warn">
    <h1>Vous n'êtes pas connecté</h1>
    <p>Gérez et publiez vos événements en toute simplicité avec <?= htmlspecialchars(SITE_NAME) ?> !</p>
    <hr>
    <ul>
        <li>
            Envie de faire une recherche ? 
            <a href="recherche.php">Allez ici</a>
        </li>
        <li>
            Vous voulez créer un compte ? 
            <a href="inscription.php">Allez ici</a>
        </li>
        <li>
            Vous avez déjà un compte ? 
            <a href="connexion.php">Allez ici</a>
        </li>
    </ul>
</div>

<?php endif ?>
<?php require './elements/footer.php'; ?>
