<?php 
$title = 'Inscription';


require './elements/header.php'; 
?>
<div class="auth-form">
    <h1><?= $title ?></h1>
    <form action="inscription.php" method="post">
        <input type="text"
            name="pseudo"
            placeholder="Pseudo"
            pattern="^[a-z][a-z0-9_]{2,34}$"
            required
        >
        <input type="email"
            name="email"
            placeholder="E-mail"
            required
        >
        <input type="password"
            name="password"
            pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" 
            title="8 caractères minimum, une lettre et un chiffre"
            placeholder="Mot de passe"
            required
        >
        <input type="password"
            name="confirm"
            pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" 
            title="8 caractères minimum, une lettre et un chiffre"
            placeholder="Confirmer"
            required
        >
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
    </form>
    <p class="footer">Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a></p>
</div>
<?php require './elements/footer.php' ?>
