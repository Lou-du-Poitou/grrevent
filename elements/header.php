<!-- Début des pages du site 
L'importer au début à chaques page:
require './elements/header.php';
-->
<?php 
require_once './config/constants.php';

require_once './elements/icon.php';

require_once './class/User.php';
require_once './class/Logged.php';
require_once './class/HostUrl.php';

function headerItem(string $link, string $iconName): string
/**
 * Item du header
 * 
 * @var string $link
 * @var string $icon
 * 
 * @return string (Composant html)
 */
{
    $icon = icon($iconName);

    $html = <<<HTML
    <li>
        <a class="header-item" href="$link">
            $icon
        </a>
    </li>
HTML;

    return $html;
}

if (!isset($title)) $title = SITE_NAME;

$logged = new Logged();

require_once './elements/nav.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?></title>

    <!-- Balises meta -->
    <?php if (isset($metaDescription)): ?>
    <meta name="description" content="<?= $metaDescription ?>">
    <meta property="og:description" content="<?= $metaDescription ?>">
    <?php endif; ?>

    <?php if (isset($metaImage)): ?>
    <meta property="og:image" content="<?= $metaImage ?>">
    <?php endif ?>

    <?php if (isset($metaKeywords)): ?>
    <meta property="og:keywords" content="<?= $metaKeywords ?>">
    <?php endif; ?>
    
    <meta property="og:title" content="<?= $title ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content=<?= str_replace(' ', '', SITE_NAME) ?>>
    <meta property="og:url" content="<?= htmlspecialchars(HostUrl::path($_SERVER['REQUEST_URI'])) ?>">
    <meta name="theme-color" content="#777777">

    <link rel="icon" 
        href="<?= SITE_ICON ?>"
    >

    <link rel="stylesheet" 
        href="/style/index.css"
    >
    <!-- FontAwesome pour les icones -->
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    >
</head>
<body>
    <header>
        <div class="left">
            <img alt="Logo"
                class="icon"
                src="<?= SITE_ICON ?>"
            >
            <h1><?= SITE_NAME ?></h1>
        </div>

        <div class="center">
            <?php if ($logged->is()): ?>
            <p><?= icon('user-lock') . ' ' . $logged->user()->getHTML('userPseudo') ?></p>
            <?php endif ?>
        </div>

        <ul class="options">
            <?php if ($logged->is()): ?>
            <?= headerItem('account.php', 'user') ?>
            <?= headerItem('deconnexion.php', 'right-from-bracket') ?>

            <?php else: ?>
            <?= headerItem('connexion.php', 'right-to-bracket') ?>

            <?php endif ?>

            <li>
                <label class="header-item">
                    <input id="togglenav" type="checkbox" hidden>
                    <?= icon('bars') ?>
                    <?= navMenu($logged) ?>
                </label>
            </li>
        </ul>
    </header>
    <main>
    <!-- Suite -->
