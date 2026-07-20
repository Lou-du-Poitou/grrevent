<?php 
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/../elements/inputs.php';

require_once __DIR__ . '/../actions/auth.actions.php';

require_once __DIR__ . '/../class/utils/Logged.php';
require_once __DIR__ . '/../class/others/FormMessage.php';

// Paramètres passés au header
$titlePage = 'Connexion';
$metaDescription = "Page de connexion de l'application";

$erreur = null;

$pseudo = '';
$email = '';

if (
    isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['SCRIPT_NAME']) && 
    strpos($_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME'])
) {
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
                $logged = new Logged();
                $logged->setUser($user);

                header('Location: index.php');
            } else {
                $erreur = FormMessage::getError('Login');
            }

            $db = null;
        } else {
            $erreur = FormMessage::getError('ValidationRegexFail');
        }
    }
}

require __DIR__ . '/../elements/header.php'; 
?>
<div class="auth-form">
    <h1><?= htmlspecialchars($titlePage) ?></h1>

    <form action="" method="post">
        <?= emailInput('email', 'E-mail', $email) ?>
        <?= passwordInput('password', 'Mot de passe') ?>

        <p class="reset-pass"><a href="motpasse.php">Mot de passe oublié ?</a></p>

        <?= buttonInput('Se connecter', 'submit') ?>

        <?php if ($erreur): ?>
        <!-- Erreur lors de la connexion -->
        <p class="error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif ?>
    </form>       
    
    <p class="footer">Vous n'avez pas de compte ? <a href="inscription.php">S'inscrire</a></p>
</div>
<?php require __DIR__ . '/../elements/footer.php' ?>
