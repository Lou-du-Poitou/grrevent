<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/../elements/inputs.php';

require_once __DIR__ . '/../actions/event.actions.php';

require_once __DIR__ . '/../class/utils/Logged.php';
require_once __DIR__ . '/../class/utils/HostUrl.php';
require_once __DIR__ . '/../class/utils/UploadFile.php';
require_once __DIR__ . '/../class/utils/CSRFToken.php';
require_once __DIR__ . '/../class/others/FormMessage.php';

// Paramètres passés au header
$titlePage = 'Nouveau';
$metaDescription = "Page avec formulaire de publication d'un nouvel événement";

$logged = new Logged();
$csrfToken = new CSRFToken();

$erreur = null;

$title = '';
$description = '';
$date = '';
$location = '';
$places = null;
$picture = null;

if (
    isset($_SERVER['HTTP_REFERER']) && 
    strpos($_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME']) && 
    $logged->is()
) {
    if (
        isset($_POST['csrf_token']) &&
        isset($_POST['title']) && isset($_POST['description']) && isset($_POST['date']) &&
        isset($_POST['location']) && isset($_POST['places']) &&
        count($_POST) === 6
    ) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        if ($_POST['location']) {
            $location = $_POST['location'];
        }
        if ($_POST['places']) {
            $places = (int)$_POST['places'];
        }

        // Vérification du token CSRF
        $csrfCheck = hash_equals(
            $csrfToken->get(), 
            $_POST['csrf_token']
        );

        if ($csrfCheck) {
            $userId = $logged->user()->getValue('userId');
    
            // Validation des données
            $ok = true;
            if (!preg_match(DATE_REGEX, $date)) {
                $ok = false;
                $erreur = FormMessage::getError('InvalidDate');
            }
    
            if (strlen($title) > MAX_TITLE_LENGTH) {
                $ok = false;
                $erreur = FormMessage::getError('TooLongTitle');
            }
    
            if (strlen($description) > MAX_DESCRIPTION_LENGTH) {
                $ok = false;
                $erreur = FormMessage::getError('TooLongDescription');
            }
    
            if (strlen($location) > MAX_LOCATION_LENGTH) {
                $ok = false;
                $erreur = FormMessage::getError('TooLonglocation');
            }
            
            // Taitement de l'image
            if (isset($_FILES['picture'])  && $_FILES['picture']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['picture'];
                
                $upload = new UploadFile($file);
                if (!$upload->checkSize()) {
                    $ok = false;
                    $erreur = FormMessage::getError('MaxSizeFile');
                }
    
                $mimeType = $upload->checkImgFormat();
                if (!$mimeType) {
                    $ok = false;
                    $erreur = FormMessage::getError('InvalidFormat');
                }
    
                $status = $upload->getStatus();
                if ($status !== UPLOAD_ERR_OK) {
                    $ok = false;
                    $erreur = FormMessage::getUpload($status);
                }
            }
    
            if ($ok) {
                $db = connection();
    
                if (isset($upload) && isset($mimeType)) {
                    $pictureName = uniqid((string)$userId . '-');
    
                    $picture = PICTURE_EVENT_PATH . '/' . $pictureName . '.' . IMAGE_FORMATS[$mimeType];
    
                    $picturePath = getcwd() . $picture;
    
                    $upload->moveFile($picturePath);
                }
    
                $eventId = createEvent($db,
                    $title,
                    $description,
                    $date,
                    $location,
                    $places,
                    $picture,
                    $userId
                );
    
                $db = null;
    
                $eventUrl = HostUrl::pathToEvent($eventId);
                header("Location: $eventUrl");
            }
        } else {
            $erreur = FormMessage::getError('InvalidCSRFToken');
        }
    }
}

require __DIR__ . '/../elements/header.php';
?>
<?php if ($logged->is()): ?>
<div class="container">
    <h1 class="form-title">Nouvel événement</h1>
    <form action="" method="post" enctype="multipart/form-data" class="form-container event">
        <?= textInput('title', 'Titre de votre événement', $title) ?>
        <?= textareaInput('description', 'Décrivez votre événement ici...', $description) ?>
        <?= datetimeInput('date', $date) ?>
        <?= textInput('location', 'Localisation (optionnel)', $location, false) ?>
        <?= numberInput('places', 'Places (optionnel)', $places, 1, false) ?>
        <?= fileInput('picture', 'image/png, image/jpg, image/jpeg', false) ?>

        <!-- CSRF Token -->
        <?= hiddenInput("csrf_token", $csrfToken->get()) ?>

        <?= buttonInput('Publier', 'submit') ?>

        <?php if ($erreur): ?>
        <!-- Erreur -->
        <p class="error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif ?>
    </form>
</div>

<?php else: ?>
<div class="alert-warn">
    <h1>Vous n'êtes pas connecté</h1>
    <p>Connectez-vous pour publier des événements</p>
</div>

<?php endif ?>
<?php require __DIR__ . '/../elements/footer.php'; ?>
