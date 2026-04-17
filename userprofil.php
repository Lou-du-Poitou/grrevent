<?php
session_start();

// Simulation utilisateur connecté
$_SESSION['nom'] = "Jean Dupont";
$_SESSION['pseudo'] = "jean123";

$nom = $_SESSION['nom'];
$pseudo = $_SESSION['pseudo'];

// biographie
$fichierBio = "bio.txt";

if (isset($_POST["bio"])) {
    file_put_contents($fichierBio, $_POST["bio"]);
}

$bio = file_exists($fichierBio) ? file_get_contents($fichierBio) : "Aucune bio.";

// photo profil
$photo = "default.png";

if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    move_uploaded_file($_FILES["photo"]["tmp_name"], "photo.png");
}

if (file_exists("photo.png")) {
    $photo = "photo.png";
}

// events (simulation)
$events = [
    ["id" => 1, "nom" => "Soirée Jeux"],
    ["id" => 2, "nom" => "Foot entre amis"],
    ["id" => 3, "nom" => "Sortie cinéma"]
];

// supprimer evnet
if (isset($_POST["delete"])) {
    echo "Event supprimé (à connecter à une vraie base)";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="settings.css">
<link rel="stylesheet" href="header.css">
<link rel="stylesheet" href="userprofil.css">
<title>Profil</title>
</head>

<body>

<header>

    <!--logo-->
    <div class="left">
        <img class="icon" src="logo.png" alt="Logo">
        <!--nom dynamique-->
        <h1>Profil de <?php echo $nom; ?></h1>
    </div>

    <ul class="options">

        <!-- accès profil -->
        <li>
            <a class="nav-item" href="profil.php">👤</a>
        </li>

        <!-- accès menu -->
        <li>
            <label class="nav-item">
                <input id="togglenav" type="checkbox" hidden>
                ☰
                <nav>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="profil.php">Profil</a></li>
                    </ul>
                </nav>
            </label>
        </li>

    </ul>

</header>

<main>

<!-- bouton Retour -->
<div class="back-btn">
<a href="<?php echo $_SERVER['HTTP_REFERER'] ?? 'index.php'; ?>">
    <button>Retour</button>
</a>
</div>


<!-- photo profil -->
 
<section class="profile-container">

<div class="profile-header">

    <img src="<?php echo $photo; ?>" class="profile-pic">

    <div class="profile-info">
        <h2><?php echo $nom; ?></h2>
        <p>@<?php echo $pseudo; ?></p>

        <p class="bio"><?php echo $bio; ?></p>

        <form method="post" class="bio-form">
            <textarea name="bio"></textarea>
            <button type="submit">Modifier</button>
        </form>
    </div>

</div>

<section>
<div class="events-container">

<?php foreach ($events as $event): ?>

<div class="event-card">
    <div class="event-title"><?php echo $event["nom"]; ?></div>

    <img src="default_event.png">

    <form method="post">
        <button>Modifier</button>
        <button>Supprimer</button>
    </form>
</div>

<?php endforeach; ?>

</div>
</section>

</main>

</body>
</html>