<?php

require_once './config/regex.php';

require_once './elements/inputs.php';


$title = 'Paramètres';

if (isset($valider1)) {
  $prenom = $_POST["prenom"];
  $nom = $_POST["nom"];
  $date_naissance = $_POST["date_naissance"];
  $adresse = $_POST["adresse"];
  $email = $_POST["email"];
  $bio = $_POST["bio"];
}

if (isset(valider2)) {
  $avis = $_POST["avis"];
}

require './elements/header.php' ?>

<link rel="stylesheet" href="parametres.css">

<h1><?= $title ?></h1>
<div id="box">

  <!-- COLONNE DE GAUCHE -->
  <div id="leftbox">
    <h2> Modifier profil </h2>

    <form method="POST">

      <div class="box1">

        <div class="field">
          <label for="prenom">Prénom</label>
          <input type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>" placeholder="écrire prénom...">
        </div>

        <div class="field">
          <label for="nom">Nom</label>
          <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" placeholder="écrire nom...">
        </div>

        <div class="field">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" name="date_naissance" value="<?= htmlspecialchars($date_naissance) ?>">
          </div>

          <div class="field">
          <label for="adresse">Adresse</label>
          <input type="text" name="adresse" value="<?= htmlspecialchars($adresse) ?>" placeholder="écrire adresse...">
        </div>

        <div class="field">
          <label for="email">E-mail</label>
          <input type="text" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="écrire adresse e-mail...">
        </div>

        <div class="field">
          <label for="bio">Biographie</label>
          <textarea name="bio" placeholder="Parlez-vous en quelques mots…"> <?= htmlspecialchars($bio) ?> </textarea>
        </div>

      </div>

      <div class="photo-box">
        <label class="photo" for="photo"> Changer la photo </label>
        <input type="file" name="photo" accept="image/*" style="display:none">
      </div>

      <div class="enregistrer">
          <button type="submit" name="valider1">Enregistrer le profil</button>
      </div>

    </form>      
  
  </div>

  <!-- COLONNE DE DROITE -->
  <div id="rightbox">

    <!-- préférences -->
    <div id="pref">
      <h2> Préférences </h2>

      <form method="POST">

        <div class="field">
          <label for="region">Région</label>
          <select id="region" name="region">
            
            <!-- choix des régions -->
            <?php $regions = [
                    'Auvergne-Rhône-Alpes', 'Bourgogne-Franche-Comté', 'Bretagne',
                    'Centre-Val de Loire', 'Corse', 'Grand Est', 'Guadeloupe',
                    'Guyane', 'Hauts-de-France', 'Île-de-France', 'La Réunion',
                    'Martinique', 'Mayotte', 'Normandie', 'Nouvelle-Aquitaine',
                    'Occitanie', 'Pays de la Loire', 'Provence-Alpes-Côte d\'Azur',
                  ];

            foreach ($regions as $r): ?>
              <option value="<?= htmlspecialchars($r) ?>"
                <?php
                  if ($r === $region) {
                    echo 'selectionnée';
                  } else {
                    echo '';
                  }
                ?> 
              > 
                <?= htmlspecialchars($r) ?>
              </option>
            <?php endforeach; ?>

          </select>
        </div>

        <div class="field">
          <label for="avis">Votre avis</label>
          <textarea name="avis" placeholder="Partagez votre avis"> <?= htmlspecialchars($avis) ?> </textarea>
        </div>

        <div class="enregistrer">
          <button type="submit" name="valider2">Enregistrer</button>
        </div>

      </form>

    </div>

    <!-- confidentialité -->
    <div id="conf">
      <h2> Confidentialité </h2>

      <form method="POST">
        
        <input type="radio" name="visibilite" value="public"
        <?php if ($visibilite === 'public') { echo 'checked'; } ?>>
        Public
        <br>

        <input type="radio" name="visibilite" value="amis"
        <?php if ($visibilite === 'amis') { echo 'checked'; } ?>>
        Amis proches
        <br>

        <input type="radio" name="visibilite" value="prive"
        <?php if ($visibilite === 'prive') { echo 'checked'; } ?>>
        Privé
        <br>

        <div class="enregistrer">
          <button type="submit" name="valider">Enregistrer</button>
        </div>

      </form>

    </div>

  </div>

</div>
</main>

<?php require './elements/footer.php' ?>
