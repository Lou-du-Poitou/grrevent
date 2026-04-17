<?php 
require_once './config/connection.php';
require_once './config/regex.php';

require_once './elements/inputs.php';

require_once './actions/auth.actions.php';

require_once './class/FormMessage.php';

$title = 'Connexion';

$erreur = null;

$pseudo = '';
$email = '';

if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'connexion.php')) {
    if (
        isset($_POST['email']) && !empty($_POST['email']) &&
        isset($_POST['password']) && !empty($_POST['password']) &&
        count($_POST) === 2
    ) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $ok = true;
        if (!preg_match(EMAIL_REGEX, $email)) {
            $ok = false;
        }

        if ($ok) {
            $db = connection();

            $user = login($db, $email, $password);
            if ($user) {
                session_start();
                $_SESSION['user'] = $user;

                header('Location: _debug.php');
            } else {
                $erreur = FormMessage::getError('Login');
            }

            $db = null;
        } else {
            $erreur = FormMessage::getError('ValidationRegexFail');
        }
    }
}

require './elements/header.php'; 
?>
<div class="auth-form">
    <h1><?= $title ?></h1>

    <form action="connexion.php" method="post">
        <?= emailInput('email', 'E-mail', $email) ?>
        <?= passwordInput('password', 'Mot de passe') ?>

        <p class="reset-pass"><a href="motpasse.php">Mot de passe oublié ?</a></p>

        <?= buttonInput('Se connecter', 'submit') ?>

        <?php if ($erreur): ?>
        <!-- Erreur lors de la connexion -->
        <p class="error"><?= $erreur ?></p>
        <?php endif ?>
    </form>       
    
    <p class="footer">Vous n'avez pas de compte ? <a href="inscription.php">S'inscrire</a></p>
</div>
<?php require './elements/footer.php' ?>
