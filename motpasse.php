<?php 
require_once './config/connection.php';
require_once './config/constants.php';

require_once './elements/inputs.php';

require_once './actions/auth.actions.php';

require_once './class/FormMessage.php';

// Paramètres passés au header
$title = 'Mot de passe oublié';
$metaDescription = "Page à utiliser en cas d'oublie de votre mot de passe";

// false si on réinitialise le mot de passe (Affichage du formulaire)
$request = true;

$erreur = null;
$success = null;

$email = '';

if (isset($_GET['token']) && isset($_GET['id'])) {
    $token = $_GET['token'];
    $id = (int)$_GET['id'];
    
    $ok = true;
    if (!preg_match(TOKEN_REGEX, $token)) {
        $ok = false;
    }

    if ($ok) {
        $request = false;
    }
}

if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'motpasse.php')) {
    if (isset($token) && isset($id)) {
        // On lance la procédure pour changer le mot de passe

        if (
            isset($_POST['password']) && !empty($_POST['password']) &&
            isset($_POST['confirm']) && !empty($_POST['confirm']) &&
            count($_POST) === 2
        ) {
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];

            $ok = true;
            if (
                !preg_match(PASSWORD_REGEX, $password) || 
                !preg_match(PASSWORD_REGEX, $confirm)
            ) {
                $ok = false;
            }

            if ($ok) {
                if ($password !== $confirm) {
                    $erreur = FormMessage::getError('PasswordNotSame');
                } else {
                    $db = connection();

                    $change = resetPassword($db, $id, $token, $password);
                    if ($change) {
                        $success = FormMessage::getSuccess('PasswordUpdated');
                    } else {
                        $erreur = FormMessage::getError('InvalidToken');
                    }

                    $db = null;
                }
            } else {
                $erreur = FormMessage::getError('ValidationRegexFail');
            }
        }
    } else {
        // On lance la procédure pour envoyer le token

        if (
            isset($_POST['email']) && !empty($_POST['email']) &&
            count($_POST) === 1
        ) {
            $email = $_POST['email'];

            $ok = true;
            if (!preg_match(EMAIL_REGEX, $email)) {
                $ok = false;
            }

            if ($ok) {
                $db = connection();
                $request = requestResetPassword($db, $email);

                if ($request) {
                    $success = FormMessage::getSuccess('EmailSent');
                } else {
                    $erreur = FormMessage::getError('InvalidEmail');
                }

                $db = null;
            } else {
                $erreur = FormMessage::getError('ValidationRegexFail');
            }
        }
    }
}

require './elements/header.php'; 
?>
<div class="auth-form">
    <h1><?= $title ?></h1>

    <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
        <?php if ($request): ?>
        <?= emailInput('email', 'E-mail', $email) ?>

        <?php else: ?>
        <?= passwordInput('password', 'Nouveau mot de passe') ?>
        <?= passwordInput('confirm', 'Confirmer') ?>

        <?php endif ?>
        
        <?= buttonInput('Soumettre', 'submit', !empty($success)) ?>

        <?php if ($erreur): ?>
        <!-- Erreur lors de la demande -->
        <p class="error"><?= $erreur ?></p>
        <?php endif ?>

        <?php if ($success): ?>
        <!-- Succès de la demande -->
        <p class="success"><?= $success ?></p>
        <?php endif ?>
    </form>       
    
    <p class="footer">Vous avez retrouvé la mémoire ? <a href="connexion.php">Se connecter</a></p>
</div>
<?php require './elements/footer.php' ?>
