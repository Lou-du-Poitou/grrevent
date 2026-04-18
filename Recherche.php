<?php

$type = isset($_GET['type']) ? $_GET['type'] : 'spectacles'; 

$recherche = isset($_GET['q']) ? $_GET['q'] : ''; 


try {
    $bdd = new PDO('mysql:host=localhost;dbname=projet_spectacles;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
    die('Erreur de connexion à la base de données.');
}

$resultats = [];

if ($type === 'spectacles') {
   
    $sql = "SELECT * FROM evenements WHERE titre LIKE :recherche OR description LIKE :recherche ORDER BY id DESC";
} else {
  
    $sql = "SELECT * FROM utilisateurs WHERE pseudo LIKE :recherche ORDER BY id DESC";
}


$stmt = $bdd->prepare($sql);

$stmt->execute(['recherche' => '%' . $recherche . '%']); 
$resultats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Recherches</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="Recherches.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="left"><h1>Nom site</h1></div>
    </header>

    <main>
        <form action="recherche.php" method="GET" style="width: 100%; display: flex; justify-content: center;">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
            
            <div class="barre-recherche">
                <button type="submit" style="border:none; background:transparent; padding:0;">
                    <img src="loupe.png" alt="Loupe" class="icone icone-gauche" />
                </button>
                
                <input type="text" name="q" placeholder="Rechercher" value="<?php echo htmlspecialchars($recherche); ?>" />
                
                <img src="engre.png" alt="Filtres" class="icone icone-droite" />
            </div>
        </form>

        <div class="boutons">
            <a href="?type=spectacles&q=<?php echo urlencode($recherche); ?>" 
               class="spec <?php if($type === 'spectacles') echo 'onglet-actif'; ?>">Spectacles</a>
               
            <a href="?type=utilisateurs&q=<?php echo urlencode($recherche); ?>" 
               class="user <?php if($type === 'utilisateurs') echo 'onglet-actif'; ?>">Utilisateurs</a>
        </div>
        
        <div class="grille-evenements">
            <?php if (!empty($resultats)): ?>
                
                <?php if ($type === 'spectacles'): ?>
                    <?php foreach($resultats as $item): ?>
                        <div class="carte-evenement">
                            <?php $image = !empty($item['image_url']) ? $item['image_url'] : 'https://picsum.photos/300/200'; ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="Image" class="carte-image">
                            
                            <div class="carte-contenu">
                                <h3 class="carte-titre"><?php echo htmlspecialchars($item['titre']); ?></h3>
                                <p class="carte-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                <button class="carte-bouton">Voir plus</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                
                <?php else: ?>
                    <?php foreach($resultats as $item): ?>
                        <div class="carte-evenement" style="align-items: center; padding: 20px;">
                            <i class="fas fa-user-circle" style="font-size: 80px; color: #ccc; margin-bottom: 15px;"></i>
                            <div class="carte-contenu">
                                <h3 class="carte-titre"><?php echo htmlspecialchars($item['pseudo']); ?></h3>
                                <button class="carte-bouton">Voir le profil</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php else: ?>
                <p>Aucun résultat trouvé pour "<?php echo htmlspecialchars($recherche); ?>" dans les <?php echo htmlspecialchars($type); ?>.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>