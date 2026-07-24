<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/../elements/inputs.php';
require_once __DIR__ . '/../elements/profiles.php';

require_once __DIR__ . '/../actions/event.actions.php';

require_once __DIR__ . '/../class/models/Event.php';
require_once __DIR__ . '/../class/models/User.php';
require_once __DIR__ . '/../class/utils/Logged.php';
require_once __DIR__ . '/../class/utils/HostUrl.php';
require_once __DIR__ . '/../class/utils/HostPath.php';

$logged = new Logged();

// Initialisation des données de l'événement
$eventId = 0;
$eventTitle = null;
$eventDescription = null;
$eventDate = null;
$eventLocation = null;
$eventPlaces = null;
$eventPicture = DEFAULT_EVENT_PICTURE;
$authorId = null;
$authorPseudo = null;

// Variable du status d'ajout d'un événement
$isAdded = false;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $ok = (bool)$id;

    if ($ok) {
        $db = connection();

        $event = selectEvent($db, $id);

        if ($event) {
            // Modification des données de l'événement
            $eventId = $event->getValue('eventId');
            $eventTitle = $event->getValue('eventTitle');
            $eventDescription = $event->getValue('eventDescription');
            $eventDate = date_format(
                date_create($event->getValue('eventDate')),
                'd/m/Y à H\hi'
            );
            $eventLocation = $event->getValue('eventLocation');
            $eventPlaces = $event->getValue('eventPlaces');
            $eventPicture = $event->getValue('eventPicture') ? 
                $event->getValue('eventPicture') : 
                $eventPicture;

            $author = $event->getValue('author');
            $authorId = $author->getValue('userId');
            $authorPseudo = $author->getValue('userPseudo');

            // Paramètres passé au header
            $titlePage = $eventTitle;
            $metaDescription = $eventDescription;
            $metaImage = HostUrl::path($eventPicture);
            $metaKeywords = $eventLocation;

            // Initialisation du status d'ajout de l'événement
            if ($logged->is()) {
                $isAdded = isAddedEvent($db, 
                    $logged->user()->getValue('userId'),
                    $eventId
                );
            }

            // Paramètre passé au header
            $metaAuthor = $authorPseudo;
        }

        $db = null;
    }
}

require __DIR__ . '/../elements/header.php';
?>

<?= backButton() ?>

<?php if (isset($event) && !empty($event)): ?>
<div class="container">
    <div class="profile-data">

        <div class="image-container">
            <img src="<?= htmlspecialchars($eventPicture) ?>" 
                class="profile-pic event-pic"
                alt="Photo de l'événement <?= htmlspecialchars($eventId) ?>"
            >
        </div>

        <div>
            <div class="profile-header">
                <h1 class="event-title">
                    <?= htmlspecialchars($eventTitle) ?>
                </h1>

                <?php if (
                    $logged->is() && 
                    isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SCRIPT_NAME'])
                ): ?>
                <?= addEventHandler($event, 
                    $_SERVER['SCRIPT_NAME'], 
                    $_SERVER['REQUEST_URI'], 
                    $isAdded
                ) ?>

                <?php endif ?>
            </div>

            <?php if ($logged->is() && 
                $logged->user()->getValue('userId') === $authorId &&
                isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SCRIPT_NAME'])
            ): ?>
            <div class="author-actions">
                <?= deleteEventHandler($event, 
                    $_SERVER['SCRIPT_NAME'], 
                    $_SERVER['REQUEST_URI']
                ) ?>
            </div>
            <?php endif ?>

            <div class="profile-headers">
                <?= profileHeader('Le', $eventDate) ?>
                <?= profileHeader('Par', $authorPseudo, HostPath::toUser($authorPseudo)) ?>
                <?php if (!empty($eventLocation)): ?>
                <?= profileHeader('Localisation', $eventLocation) ?>
                <?php endif ?>
                <?php if (!empty($eventPlaces)): ?>
                <?= profileHeader('Places', $eventPlaces) ?>
                <?php endif ?>
            </div>

            <p class="event-description">
                <?= nl2br(htmlspecialchars($eventDescription)) ?>
            </p>
        </div>

    </div>

</div>

<?php else: ?>
<div class="alert-warn">
    <h1>Événement inconnu</h1>
    <p>Vérifiez l'URL ou l'existence de cet événement</p>
</div>

<?php endif ?>

<?php require __DIR__ . '/../elements/footer.php';
