<!--
ProjetWeb - L1 INF09 (c) 2026
-->
<!-- Début des pages du site 
L'importer au début à chaques page:
require './elements/header.php';
-->
<?php 
require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/nav.php';
require_once __DIR__ . '/icon.php';

require_once __DIR__ . '/../class/models/User.php';
require_once __DIR__ . '/../class/utils/Logged.php';
require_once __DIR__ . '/../class/utils/HostUrl.php';
require_once __DIR__ . '/../class/utils/HostPath.php';

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
    $link = htmlspecialchars($link);
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

if (!isset($titlePage)) $titlePage = SITE_NAME;
if (!isset($metaImage)) $metaImage = HostUrl::path(SITE_PNG_ICON);

$logged = new Logged();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($titlePage) ?></title>

    <!-- Balises meta -->
    <?php if (isset($metaDescription)): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <?php endif; ?>
    <?php if (isset($metaKeywords)): ?>
    <meta property="og:keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <?php endif; ?>
    <?php if (isset($metaAuthor)): ?>
    <meta property="og:author" content="<?= htmlspecialchars($metaAuthor) ?>">
    <?php endif; ?>
    <meta property="og:image" content="<?= htmlspecialchars($metaImage) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($titlePage) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content=<?= htmlspecialchars(str_replace(' ', '', SITE_NAME)) ?>>
    <meta property="og:url" content="<?= htmlspecialchars(HostUrl::path($_SERVER['REQUEST_URI'])) ?>">
    <meta name="theme-color" content="#777777">

    <!-- Icônes du site -->
    <link rel="icon" 
        href="<?= htmlspecialchars(SITE_PNG_ICON) ?>"
        type="image/png"
    >
    <link rel="shortcut icon"
        href="<?= htmlspecialchars(SITE_ICO_ICON) ?>"
        type="image/x-icon"
    >

    <!-- Ajout du style -->
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
                src="<?= htmlspecialchars(SITE_PNG_ICON) ?>"
            >
            <h1><?= htmlspecialchars(SITE_NAME) ?></h1>
        </div>

        <div class="center">
            <?php if ($logged->is()): ?>
            <p><?= icon('user-lock') . ' ' . $logged->user()->getHTML('userPseudo') ?></p>
            <?php endif ?>
        </div>

        <ul class="options">
            <?php if ($logged->is()): ?>
            <?= headerItem(HostPath::toUser($logged->user()->getValue('userPseudo')), 'user') ?>
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
