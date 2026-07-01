<?php
require_once './config/connection.php';
require_once './config/constants.php';

require_once './elements/inputs.php';
require_once './elements/profiles.php';
require_once './elements/cards.php';

require_once './actions/user.actions.php';

require_once './class/utils/Logged.php';

$logged = new Logged();

// Initialisation des variables d'affichage
$userId = 0;
$userPseudo = null;
$userName = null;
$userPicture = DEFAULT_USER_PICTURE;
$userBiography = null;
$userLocation = null;
$userJoinedAt = null;

// Variable du suivi d'un utilisateur
$isFollow = false;

if (isset($_GET['pseudo'])) {
    $pseudo = $_GET['pseudo'];
    
    $ok = true;
    if (!preg_match(PSEUDO_REGEX, $pseudo)) {
        $ok = false;
    }

    if ($ok) {
        $db = connection();

        $user = selectUser($db, $pseudo);

        if ($user) {
            // Paramètres passé au header
            $titlePage = 'Profil de ' . $user->getValue('userPseudo');
            $metaDescription = $user->getValue('userBiography');
            if ($user->getValue('userPicture')) {
                $metaImage = HostUrl::path($user->getValue('userPicture'));
            }
            $metaKeywords = $user->getValue('userLocation');
            $metaAuthor = $user->getValue('userPseudo');

            // Initialisation du suivi de l'utilisateur connecté
            if ($logged->is()) {
                $isFollow = isFollowUser($db, 
                    $logged->user()->getValue('userId'), 
                    $user->getValue('userId')
                );
            }

            // Modification des variables d'affichage
            $userId = $user->getHTML('userId');
            $userPseudo = $user->getHTML('userPseudo');
            $userName = empty($user->getValue('userName')) ? 
                $user->getHTML('userPseudo') : 
                $user->getHTML('userName');
            $userBiography = nl2br($user->getHTML('userBiography'));
            $userLocation = $user->getHTML('userLocation');
            $userJoinedAt = date_format(
                date_create($user->getValue('userJoinedAt')),
                'd/m/Y'
            );
            if (!empty($user->getValue('userPicture'))) {
                $userPicture = $user->getHTML('userPicture');
            }
        }

        $offset = 0;
        if (isset($_GET['offset'])) {
            $offset = (int)$_GET['offset'];
        }

        if ($offset >= 0 && $user) {
            $events = selectUserEvents($db,
                $user->getValue('userId'),
                DEFAULT_SELECT_LIMIT,
                $offset
            );
        }

        $db = null;
    }
}

require './elements/header.php';
?>

<?= backButton() ?>

<?php if (isset($user) && !empty($user)): ?>
<div class="container">
    <div class="profile-data">

        <div class="image-container">
            <img src="<?= $userPicture ?>" 
                class="profile-pic user-pic"
                alt="Photo de <?= $userPseudo ?>"
            >
        </div>

        <div>
            <div class="profile-header">
                <h1 class="user-name">
                    <?= $userName ?>
                </h1>

                <?php if ($logged->is() && isset($_SERVER['REQUEST_URI'])): ?>
                <?= followUserHandler($user, $_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'], $isFollow) ?>

                <?php endif ?>
            </div>

            <p class="user-pseudo">
                <b>@</b><?= $userPseudo ?>
            </p>

            <div class="profile-headers">
                <?= profileHeader('À rejoint le', $userJoinedAt) ?>
                <?php if (!empty($userLocation)): ?>
                <?= profileHeader('Localisation', $userLocation) ?>
                <?php endif ?>
            </div>

            <p class="user-bio">
                <?= $userBiography ?>
            </p>
        </div>

    </div>

    <!-- Événements de l'utilisateur -->
    <?php if (isset($events) && isset($offset)): ?>
    <?= cardsThread($events, HostUrl::pathToUser($user->getValue('userPseudo')), $offset) ?>

    <?php endif ?>
</div>

<?php else: ?>
<div class="alert-warn">
    <h1>Utilisateur inconnu</h1>
    <p>Vérifiez l'URL ou l'existence de ce compte</p>
</div>

<?php endif ?>

<?php require './elements/footer.php'; ?>
