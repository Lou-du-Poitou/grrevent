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

$logged = new Logged();

// Initialisation des variables d'affichage
$eventId = 0;
$eventTitle = null;
$eventDescription = null;
$eventDate = null;
$eventLocation = null;
$eventPlaces = null;
$eventPicture = htmlspecialchars(DEFAULT_EVENT_PICTURE);
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
            // Paramètres passé au header
            $titlePage = $event->getValue('eventTitle');
            $metaDescription = $event->getValue('eventDescription');
            if ($event->getValue('eventPicture')) {
                $metaImage = HostUrl::path($event->getValue('eventPicture'));
            } else {
                $metaImage = HostUrl::path(DEFAULT_EVENT_PICTURE);
            }
            $metaKeywords = $event->getValue('eventLocation');

            // Initialisation du status d'ajout de l'événement
            if ($logged->is()) {
                $isAdded = isAddedEvent($db, 
                    $logged->user()->getValue('userId'),
                    $event->getValue('eventId')
                );
            }

            // Modification des variables d'affichage
            $eventId = $event->getHTML('eventId');
            $eventTitle = $event->getHTML('eventTitle');
            $eventDescription = nl2br($event->getHTML('eventDescription'));
            $eventDate = date_format(
                date_create($event->getValue('eventDate')),
                'd/m/Y à H\hi'
            );
            $eventLocation = $event->getHTML('eventLocation');
            $eventPlaces = $event->getHTML('eventPlaces');
            if (!empty($event->getValue('eventPicture'))) {
                $eventPicture = $event->getHTML('eventPicture');
            }

            $author = $event->getValue('author');
            $authorId = $author->getHTML('userId');
            $authorPseudo = $author->getHTML('userPseudo');

            // Paramètre passé au header
            $metaAuthor = $author->getValue('userPseudo');
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
            <img src="<?= $eventPicture ?>" 
                class="profile-pic event-pic"
                alt="Photo de l'événement <?= $eventId ?>"
            >
        </div>

        <div>
            <div class="profile-header">
                <h1 class="event-title">
                    <?= $eventTitle ?>
                </h1>

                <?php if ($logged->is() && isset($_SERVER['REQUEST_URI'])): ?>
                <?= addEventHandler($event, $_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'], $isAdded) ?>

                <?php endif ?>
            </div>

            <?php if ($logged->is() && 
                $logged->user()->getValue('userId') === $author->getValue('userId') &&
                isset($_SERVER['REQUEST_URI'])
            ): ?>
            <div class="author-actions">
                <?= deleteEventHandler($event, $_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI']) ?>
            </div>
            <?php endif ?>

            <div class="profile-headers">
                <?= profileHeader('Le', $eventDate) ?>
                <?= profileHeader('Par', $authorPseudo, HostUrl::pathToUser($authorPseudo)) ?>
                <?php if (!empty($eventLocation)): ?>
                <?= profileHeader('Localisation', $eventLocation) ?>
                <?php endif ?>
                <?php if (!empty($eventPlaces)): ?>
                <?= profileHeader('Places', $eventPlaces) ?>
                <?php endif ?>
            </div>

            <p class="event-description">
                <?= $eventDescription ?>
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
