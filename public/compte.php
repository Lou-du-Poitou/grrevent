<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/../elements/inputs.php';

require_once __DIR__ . '/../actions/user.actions.php';

require_once __DIR__ . '/../class/utils/Logged.php';
require_once __DIR__ . '/../class/utils/HostUrl.php';
require_once __DIR__ . '/../class/utils/UploadFile.php';
require_once __DIR__ . '/../class/utils/CSRFToken.php';
require_once __DIR__ . '/../class/others/FormMessage.php';

// Paramètres passés au header
$titlePage = 'Compte';
$metaDescription = "Page pour modifier son compte";

$logged = new Logged();
$csrfToken = new CSRFToken();

$erreur = null;
if ($logged->is()) {
    $user = $logged->user();

    $name = $user->getValue('userName');
    $biography = $user->getValue('userBiography');
    $picture = $user->getValue('userPicture');
    $location = $user->getValue('userLocation');
}

if (
    isset($_SERVER['HTTP_REFERER']) && 
    strpos($_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME']) && 
    $logged->is()
) {
    if (
        isset($_POST['csrf_token']) &&
        isset($_POST['name']) && isset($_POST['biography']) && isset($_POST['location']) &&
        count($_POST) === 4
    ) {
        $name = $_POST['name'];
        $location = $_POST['location'];
        $biography = $_POST['biography'];

        // Vérification du token CSRF
        $csrfCheck = hash_equals(
            $csrfToken->get(), 
            $_POST['csrf_token']
        );

        if ($csrfCheck) {
            $userId = $logged->user()->getValue('userId');
    
            // Validation des données}
            $ok = true;
    
            if (strlen($name) > MAX_NAME_LENGTH) {
                $ok = false;
                $erreur = FormMessage::getError('TooLongName');
            }
    
            if (strlen($biography) > MAX_BIOGRAPHY_LENGTH) {
                $ok = false;
                $erreur = FormMessage::getError('TooLongBiography');
            }
    
            if (strlen($location) > MAX_LOCATION_LENGTH) {
                $ok = false;
                $erreur = FormMessage::getError('TooLonglocation');
            }
    
            // Taitement de l'image
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] !== UPLOAD_ERR_NO_FILE) {
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
                    $pictureName = (string)$userId;
    
                    $picture = PICTURE_USER_PATH . '/' . $pictureName . '.' . IMAGE_FORMATS[$mimeType];
    
                    $picturePath = getcwd() . $picture;
    
                    if (file_exists($picturePath)) {
                        unlink($picturePath);
                    }
    
                    $upload->moveFile($picturePath);
                }
    
                $user = updateUser($db,
                    $userId,
                    $name,
                    $biography,
                    $picture,
                    $location
                );
    
                $db = null;
    
                $logged->setUser($user);
    
                $userPseudo = $user->getValue('userPseudo');
                $userUrl = HostUrl::pathToUser($userPseudo);
    
                header("Location: $userUrl");
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
    <h1 class="form-title">Votre compte</h1>
    <form action="" method="post" enctype="multipart/form-data" class="form-container user">
        <?= textInput('name', "Nom d'utilisateur (optionnel)", $name, false) ?>
        <?= textInput('location', 'Localisation (optionnel)', $location, false) ?>
        <?= textareaInput('biography', 'Parlez un peu de vous ici...', $biography, false) ?>
        <?= fileInput('picture', 'image/png, image/jpg, image/jpeg', false) ?>

        <!-- CSRF Token -->
        <?= hiddenInput("csrf_token", $csrfToken->get()) ?>

        <p class="footer">Changer le <a href="motpasse.php">mot de passe</a></p>

        <?= buttonInput('Modifier', 'submit') ?>

        <?php if ($erreur): ?>
        <!-- Erreur -->
        <p class="error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif ?>
    </form>
</div>

<?php else: ?>
<div class="alert-warn">
    <h1>Vous n'êtes pas connecté</h1>
    <p>Connectez-vous pour modifier votre compte</p>
</div>

<?php endif ?>
<?php require __DIR__ . '/../elements/footer.php'; ?>
