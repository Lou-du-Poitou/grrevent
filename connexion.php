<?php 
$title = 'Connexion';


require './elements/header.php'; 
?>
<div class="auth-form">
    <h1><?= $title ?></h1>
    <form action="connexion.php" method="post">
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
        <p class="reset-pass"><a href="motpasse.php">Mot de pass oublié ?</a></p>
        <button type="submit"  
            title="Se connecter"
        >Se connecter</button>
    </form>       
    <p class="footer">Vous n'avez pas de compte ? <a href="inscription.php">S'inscrire</a></p>
</div>
<?php require './elements/footer.php' ?>
