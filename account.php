<?php
require_once './config/constants.php';
require_once './config/connection.php';
require_once './elements/inputs.php';

$title = 'Paramètres';
$succes = null;
$erreur = null;

// chargement des données actuelles de l'utilisateur
$r = $db->prepare("SELECT * FROM User WHERE userId = ?");
$r->execute([$_SESSION['userId']]);
$user = $r->fetch(PDO::FETCH_ASSOC);

$userName      = $user['userName']      ?? '';
$userEmail     = $user['userEmail']     ?? '';
$userBiography = $user['userBiography'] ?? '';
$userPicture   = $user['userPicture']   ?? '';
$userLocation  = $user['userLocation']  ?? '';

// Quand le bouton "valider1" est cliqué
if (isset($_POST['valider1'])) {
    $userName      = trim($_POST['userName']      ?? '');
    $userEmail     = trim($_POST['userEmail']      ?? '');
    $userBiography = trim($_POST['userBiography'] ?? '');
    $userLocation  = trim($_POST['userLocation']  ?? '');

    $r = $db->prepare("
        UPDATE User
        SET userName = ?,
            userEmail = ?,
            userBiography = ?,
            userLocation = ?
        WHERE userId = ?
    ");
    $r->execute([
        $userName,
        $userEmail,
        $userBiography,
        $userLocation,
        $_SESSION['userId']
    ]);

    $succes = 'Profil mis à jour.';
}

// Quand le bouton "valider2" est cliqué
if (isset($_POST['valider2'])) {
    $userLocation = trim($_POST['userLocation'] ?? '');
    $avis         = trim($_POST['avis']         ?? '');


    $r = $db->prepare("
        UPDATE User
        SET userLocation = ?
        WHERE userId = ?
    ");
    $r->execute([
        $userLocation,
        $_SESSION['userId']
    ]);

    $succes = 'Préférences enregistrées.';
}

require './elements/header.php';
?>

<link rel="stylesheet" href="./style/account.css">

<main id="settings-page">

<!-- Message de succès en haut de page -->
<?php if ($succes): ?>
    <p class="msg-succes"><?= htmlspecialchars($succes) ?></p>
<?php endif; ?>

<!-- Message d'erreur en haut de page -->
<?php if ($erreur): ?>
    <p class="msg-erreur"><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>

<h1><?= $title ?></h1>

<div id="box">

    <!-- COLONNE DE GAUCHE -->
    <div id="leftbox">
        <h2>Modifier le profil</h2>

        <form method="POST" enctype="multipart/form-data">

            <div class="photo-box">
                <img class="avatar"
                     src="<?= htmlspecialchars($photo ?? '') ?>"
                     alt="Photo de profil">
                <label class="photo-btn" for="photo">Changer la photo</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>

            <div class="field">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom"
                       value="<?= htmlspecialchars($prenom) ?>"
                       placeholder="Écrire prénom...">
            </div>

            <div class="field">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($nom) ?>"
                       placeholder="Écrire nom...">
            </div>

            <div class="field">
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance"
                       value="<?= htmlspecialchars($date_naissance) ?>">
            </div>

            <div class="field">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse"
                       value="<?= htmlspecialchars($adresse) ?>"
                       placeholder="Écrire adresse...">
            </div>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($email) ?>"
                       placeholder="Écrire adresse e-mail...">
            </div>

            <div class="field">
                <label for="bio">Biographie</label>
                <textarea id="bio" name="bio"
                          placeholder="Parlez-vous en quelques mots…"><?= htmlspecialchars($bio) ?></textarea>
            </div>

            <div class="enregistrer">
                <button type="submit" name="valider1">Enregistrer le profil</button>
            </div>

        </form>
    </div>

    <!-- COLONNE DE DROITE -->
    <div id="rightbox">

        <!-- Préférences -->
        <div id="pref">
            <h2>Préférences</h2>

            <form method="POST">

                <!-- Régions à prendre ou à laisser ? -->
                <div class="field">
                    <label for="region">Région</label>
                    <select id="region" name="region">
                        <?php
                        $regions = [
                            'Auvergne-Rhône-Alpes', 'Bourgogne-Franche-Comté', 'Bretagne',
                            'Centre-Val de Loire', 'Corse', 'Grand Est', 'Guadeloupe',
                            'Guyane', 'Hauts-de-France', 'Île-de-France', 'La Réunion',
                            'Martinique', 'Mayotte', 'Normandie', 'Nouvelle-Aquitaine',
                            'Occitanie', 'Pays de la Loire', "Provence-Alpes-Côte d'Azur",
                        ];
                        foreach ($regions as $re):
                        ?>
                            <option value="<?= htmlspecialchars($re) ?>"
                                <?php if ($re === $region) { echo 'selected'; } ?>>
                                <?= htmlspecialchars($re) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="avis">Votre avis</label>
                    <textarea id="avis" name="avis"
                              placeholder="Partagez votre avis..."><?= htmlspecialchars($avis) ?></textarea>
                </div>

                <div class="enregistrer">
                    <button type="submit" name="valider2">Enregistrer</button>
                </div>

            </form>
        </div>

        <!-- Confidentialité -->
        <div id="conf">
            <h2>Confidentialité</h2>

            <form method="POST">

                <div class="field visibilite">

                    <label class="radio-option">
                        <input type="radio" name="visibilite" value="public"
                            <?php if ($visibilite === 'public') { echo 'checked'; } ?>>
                        Public
                    </label>

                    <label class="radio-option">
                        <input type="radio" name="visibilite" value="amis"
                            <?php if ($visibilite === 'amis') { echo 'checked'; } ?>>
                        Amis proches uniquement
                    </label>

                    <label class="radio-option">
                        <input type="radio" name="visibilite" value="prive"
                            <?php if ($visibilite === 'prive') { echo 'checked'; } ?>>
                        Privé
                    </label>

                </div>

                <div class="enregistrer">
                    <button type="submit" name="valider">Enregistrer</button>
                </div>

            </form>
        </div>

    </div>
</div>
</main>

<?php require './elements/footer.php' ?>