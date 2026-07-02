<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/../elements/cards.php';

require_once __DIR__ . '/../actions/user.actions.php';
require_once __DIR__ . '/../actions/event.actions.php';

require_once __DIR__ . '/../class/utils/Logged.php';
require_once __DIR__ . '/../class/utils/HostUrl.php';

// Paramètres passés au header
$titlePage = 'Suivis';
$metaDescription = "Page de suivis des utilisateurs de l'application";

$logged = new Logged();

$offset = 0;
if (isset($_GET['offset'])) {
    $offset = (int)$_GET['offset'];
}

if ($offset >= 0) {
    if ($logged->is()) {
        $db = connection();

        $userId = $logged->user()->getValue('userId');

        $users = selectFollowedUsers($db, 
            $userId,
            DEFAULT_SELECT_LIMIT,
            $offset
        );

        $db = null;
    }
}

require __DIR__ . '/../elements/header.php';
?>
<?php if ($logged->is()): ?>
<div class="container">
    <?php if (isset($users) && isset($offset)): ?>
    <h1 class="part-title">Utilisateurs suivi·es</h1>
    <?= cardsThread($users, HostUrl::path($_SERVER['SCRIPT_NAME']), $offset) ?>

    <?php endif ?>
</div>

<?php else: ?>
<div class="alert-warn">
    <h1>Vous n'êtes pas connecté</h1>
    <p>Connectez-vous pour accéder aux utilisateurs que vous suivez</p>
</div>

<?php endif ?>
<?php require __DIR__ . '/../elements/footer.php'; ?>
