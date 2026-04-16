<?php 
require_once './config/connection.php';
require_once './config/regex.php';

require_once './elements/inputs.php';

require_once './actions/auth.actions.php';

$title = 'Inscription';

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

if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'inscription.php')) {
    if (
        isset($_POST['pseudo']) && !empty($_POST['pseudo']) &&
        isset($_POST['email']) && !empty($_POST['email']) &&
        isset($_POST['password']) && !empty($_POST['password']) &&
        isset($_POST['confirm']) && !empty($_POST['confirm']) &&
        count($_POST) === 4
    ) {
        $pseudo = strtolower($_POST['pseudo']);
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
                $erreur = "Les mots de passe ne correspondent pas";
            } else {
                // Continue
                $db = connection();

                // On vérifie que le pseudo/email n'est pas déjà pris
                $duplicate = duplicateEmailPseudo($db, $pseudo, $email);
                if (!$duplicate) {
                    $user = register($db, $pseudo, $email, $password);
                    
                    if ($user) {
                        session_start();
                        $_SESSION['user'] = $user;

                        header('Location: _debug.php');
                    } else {
                        $erreur = "Erreur de la base de données";
                    }
                } else {
                    $erreur = "Email ou pseudo déjà pris";
                }

                $db = null;
            }
        } else {
            $erreur = "Bien tenté !";
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

        <button type="submit" 
            title="Créer un compte"
        >Créer un compte</button>

        <?php if ($erreur): ?>
        <!-- Erreur lors de l'inscription -->
        <p class="error"><?= $erreur ?></p>
        <?php endif ?>
    </form>

    <p class="footer">Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a></p>
</div>
<?php require './elements/footer.php' ?>
