<?php 
require_once './config/connection.php';
require_once './config/constants.php';

require_once './elements/inputs.php';

require_once './actions/auth.actions.php';

require_once './class/FormMessage.php';
require_once './class/Logged.php';

// Paramètres passés au header
$title = 'Inscription';
$metaDescription = "Page d'inscription de l'application";

$erreur = null;

/* REGEX qui servent la vérification des données */
$regex = [
    'pseudo' => PSEUDO_REGEX,
    'email' => EMAIL_REGEX,
    'password' => PASSWORD_REGEX,
    'confirm' => PASSWORD_REGEX
];

$pseudo = '';
$email = '';

if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME'])) {
    if (
        isset($_POST['pseudo']) && !empty($_POST['pseudo']) &&
        isset($_POST['email']) && !empty($_POST['email']) &&
        isset($_POST['password']) && !empty($_POST['password']) &&
        isset($_POST['confirm']) && !empty($_POST['confirm']) &&
        count($_POST) === 4
    ) {
        $pseudo = $_POST['pseudo'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        $ok = true;
        foreach ($_POST as $key => $value) {
            if (!preg_match($regex[$key], $value)) {
                $ok = false;
            }
        }

        if ($ok) {
            if ($password !== $confirm) {
                $erreur = FormMessage::getError('PasswordNotSame');
            } else {
                // Continue
                $db = connection();

                // On vérifie que le pseudo/email n'est pas déjà pris
                $duplicate = duplicateEmailPseudo($db, $pseudo, $email);
                if (!$duplicate) {
                    $user = register($db, $pseudo, $email, $password);
                    
                    if ($user) {
                        Logged::setUser($user);

                        header('Location: _debug.php');
                    } else {
                        $erreur = FormMessage::getError('DataBase');
                    }
                } else {
                    $erreur = FormMessage::getError('DuplicateEmailPseudo');
                }

                $db = null;
            }
        } else {
            $erreur = FormMessage::getError('ValidationRegexFail');
        }
    }
}

require './elements/header.php'; 
?>
<div class="auth-form">
    <h1><?= $title ?></h1>

    <form action="inscription.php" method="post">
        <?= pseudoInput('pseudo', 'Pseudo', $pseudo) ?>
        <?= emailInput('email', 'E-mail', $email) ?>
        <?= passwordInput('password', 'Mot de passe') ?>
        <?= passwordInput('confirm', 'Confirmer') ?>

        <span class="cgu-check">
            <input type="checkbox" 
                id="cgu-check"
                required
            >
            <label
                for="cgu-check"
            >
                J'accepte les 
                <a href="conditions">conditions générales d'utilisation</a>
            </label>
        </span>

        <?= buttonInput('Créer un compte', 'submit') ?>

        <?php if ($erreur): ?>
        <!-- Erreur lors de l'inscription -->
        <p class="error"><?= $erreur ?></p>
        <?php endif ?>
    </form>

    <p class="footer">Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a></p>
</div>
<?php require './elements/footer.php' ?>
