<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/../elements/inputs.php';
require_once __DIR__ . '/../elements/profiles.php';
require_once __DIR__ . '/../elements/cards.php';

require_once __DIR__ . '/../actions/user.actions.php';

require_once __DIR__ . '/../class/utils/Logged.php';
require_once __DIR__ . '/../class/utils/HostUrl.php';
require_once __DIR__ . '/../class/utils/HostPath.php';

$logged = new Logged();

// Initialisation des données de l'utilisateur
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
            // Modification des données de l'utilisateur
            $userId = $user->getValue('userId');
            $userPseudo = $user->getValue('userPseudo');
            $userName = $user->getValue('userName') ? 
                $user->getValue('userName') : 
                $user->getValue('userPseudo');
            $userBiography = $user->getValue('userBiography');
            $userLocation = $user->getValue('userLocation');
            $userJoinedAt = date_format(
                date_create($user->getValue('userJoinedAt')),
                'd/m/Y'
            );
            $userPicture = $user->getValue('userPicture') ? 
                $user->getValue('userPicture') : 
                $userPicture;

            // Paramètres passé au header
            $titlePage = 'Profil de ' . $userPseudo;
            $metaDescription = $userBiography;
            $metaImage = HostUrl::path($userPicture);
            $metaKeywords = $userLocation;
            $metaAuthor = $userPseudo;

            // Initialisation du suivi de l'utilisateur connecté
            if ($logged->is()) {
                $isFollow = isFollowUser($db, 
                    $logged->user()->getValue('userId'), 
                    $userId
                );
            }
        }

        $offset = 0;
        if (isset($_GET['offset'])) {
            $offset = (int)$_GET['offset'];
        }

        if ($offset >= 0 && $user) {
            $events = selectUserEvents($db,
                $userId,
                DEFAULT_SELECT_LIMIT,
                $offset
            );
        }

        $db = null;
    }
}

require __DIR__ . '/../elements/header.php';
?>

<?= backButton() ?>

<?php if (isset($user) && !empty($user)): ?>
<div class="container">
    <div class="profile-data">

        <div class="image-container">
            <img src="<?= htmlspecialchars($userPicture) ?>" 
                class="profile-pic user-pic"
                alt="Photo de <?= htmlspecialchars($userPseudo) ?>"
            >
        </div>

        <div>
            <div class="profile-header">
                <h1 class="user-name">
                    <?= htmlspecialchars($userName) ?>
                </h1>

                <?php if (
                    $logged->is() && 
                    isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['REQUEST_URI'])
                ): ?>
                <?= followUserHandler($user, 
                    $_SERVER['SCRIPT_NAME'], 
                    $_SERVER['REQUEST_URI'], 
                    $isFollow
                ) ?>

                <?php endif ?>
            </div>

            <p class="user-pseudo">
                <b>@</b><?= htmlspecialchars($userPseudo) ?>
            </p>

            <div class="profile-headers">
                <?= profileHeader('À rejoint le', $userJoinedAt) ?>
                <?php if (!empty($userLocation)): ?>
                <?= profileHeader('Localisation', $userLocation) ?>
                <?php endif ?>
            </div>

            <p class="user-bio">
                <?= nl2br(htmlspecialchars($userBiography)) ?>
            </p>
        </div>

    </div>

    <!-- Événements de l'utilisateur -->
    <?php if (isset($events) && isset($offset)): ?>
    <?= cardsThread($events, HostPath::toUser($userPseudo), $offset) ?>

    <?php endif ?>
</div>

<?php else: ?>
<div class="alert-warn">
    <h1>Utilisateur inconnu</h1>
    <p>Vérifiez l'URL ou l'existence de ce compte</p>
</div>

<?php endif ?>

<?php require __DIR__ . '/../elements/footer.php'; ?>
