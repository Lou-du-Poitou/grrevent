<?php
require_once './config/connection.php';
require_once './config/constants.php';

require_once './elements/inputs.php';
require_once './elements/icon.php';
require_once './elements/cards.php';
require_once './elements/select.php';

require_once './actions/user.actions.php';
require_once './actions/event.actions.php';

require_once './class/HostUrl.php';

// Paramètres passé au header
$titlePage = 'Recherches';
$metaDescription = "Page de recherche de l'application";

$query = '';
$type = '';

// Variable envoyée au select
$get = [];

// Types de recherches
$types = [
    'event' => 'Événement', 
    'user' => 'Utilisateur'
];

if (isset($_GET['type'])) {
    // Variable envoyé au select
    $get = $_GET;

    $type = $_GET['type'];
}

if (isset($_GET['q']) && !empty($type)) {
    $query = $_GET['q'];

    $offset = 0;
    if (isset($_GET['offset'])) {
        $offset = (int)$_GET['offset'];
    }

    if ($offset >= 0) {
        // Paramètres passé au header
        $metaDescription = 'Recherche : ' . $query;
        $metaKeywords = $type;

        $db = connection();

        switch ($type) {
            case 'event':
                $results = selectSearchedEvent(
                    $db,
                    $query,
                    DEFAULT_SELECT_LIMIT,
                    $offset
                );
                break;
            case 'user':
                $results = selectSearchedUser(
                    $db,
                    $query,
                    DEFAULT_SELECT_LIMIT,
                    $offset
                );
                break;
            default:
                /* Pas de cas par défaut, en cas de type non valide
                 * le fil ne s'affiche pas.
                 */
        }

        $db = null;
    }
}

require './elements/header.php';
?>
<div class="container">
    <form action="" action="get" class="search-part">
        <?= searchInput('q', 'Saisissez votre recherche...', $query) ?>

        <?= select($types, $get) ?>

        <?= buttonInput('', 'submit', false, 'magnifying-glass') ?>
    </form>

    <?php if (isset($results) && isset($offset)): ?>
    <?= cardsThread($results, HostUrl::pathToSearch($query, $type), $offset) ?>

    <?php endif ?>
</div>
<?php require './elements/footer.php' ?>
