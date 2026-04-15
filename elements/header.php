<!-- Début des pages du site 
L'importer au début à chaques page:
require './elements/header.php';
-->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Nom site' ?></title>
    <link rel="icon" href="path_to_icon">
    <link rel="stylesheet" href="/style/index.css">
    <!-- FontAwesome pour les icones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
    <header>
        <div class="left">
            <img alt="Logo"
                class="icon"
                src="path_to_icon"
            >
            <h1>Nom site</h1>
        </div>
        <ul class="options">
            <li>
                <a class="nav-item" href="account.php">
                    <i class="fas fa-user"></i>
                </a>
            </li>
            <li>
                <label class="nav-item">
                    <input id="togglenav" type="checkbox" hidden>
                    <i class="fas fa-bars"></i>
                    <nav>
                        <ul>
                            <li class="active">
                                <a href="/">Accueil</a>
                            </li>
                            <li>
                                <a href="">Autre</a>
                            </li>
                            <li>
                                <a href="">Autre</a>
                            </li>
                        </ul>
                    </nav>
                </label>
            </li>
        </ul>
    </header>
    <main>
    <!-- Suite -->
