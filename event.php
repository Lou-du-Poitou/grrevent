<?php
require_once './config/connection.php';
require_once './config/constants.php';

require_once './elements/inputs.php';
require_once './elements/profiles.php';

require_once './actions/event.actions.php';

require_once './class/Event.php';
require_once './class/User.php';
require_once './class/Logged.php';
require_once './class/HostUrl.php';

$logged = new Logged();

// Initialisation des variables d'affichage
$eventId = 0;
$eventTitle = null;
$eventDescription = null;
$eventDate = null;
$eventLocation = null;
$eventPlaces = null;
$eventPicture = DEFAULT_USER_PICTURE;
$authorId = null;
$authorPseudo = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $ok = (bool)$id;

    if ($ok) {
        $db = connection();

        $event = selectEvent($db, $id);

        if ($event) {
            // Paramètres passé au header
            $title = 'Événement ' . $event->getHTML('eventTitle');
            $metaDescription = $event->getHTML('eventDescription');
            $metaImage = $event->getHTML('eventPicture');
            $metaKeywords = $event->getHTML('eventLocation');

            // Modification des variables d'affichage
            $eventId = $event->getHTML('eventId');
            $eventTitle = $event->getHTML('eventTitle');
            $eventDescription = nl2br($event->getHTML('eventDescription'));
            $eventDate = date_format(
                date_create($event->getValue('eventDate')),
                'd/m/Y à H:i'
            );
            $eventLocation = $event->getHTML('eventLocation');
            $eventPlaces = $event->getHTML('eventPlaces');
            if (!empty($event->getValue('eventPicture'))) {
                $userPicture = $event->getHTML('eventPicture');
            }

            $author = $event->getValue('author');
            $authorId = $author->getHTML('userId');
            $authorPseudo = $author->getHTML('userPseudo');
        }

        $db = null;
    }
}

require './elements/header.php';
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

                <?php if ($logged->is()): ?>
                <form>
                    <input type="hidden" name="add" value="<?= $eventId ?>">
                    <?= buttonInput('Ajouter', 'submit', true, 'plus') ?>
                </form>

                <?php endif ?>
            </div>

            <div class="profile-headers">
                <?= profileHeader('Le', $eventDate) ?>
                <?= profileHeader('Par', $authorPseudo, HostUrl::path("/user.php?pseudo=$authorPseudo")) ?>
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
<div class="profile-unknow">
    <h1>Événement inconnu</h1>
    <p>Vérifiez l'URL ou l'existence de cet événement</p>
</div>

<?php endif ?>

<?php require './elements/footer.php';
