<?php
require_once __DIR__ . '/../class/utils/Logged.php';

function navItem(
    string $link, 
    string $title, 
    string $className=''
): string
/**
 * Item du menu de navigation
 * 
 * @var string $link Lien vers lequel il pointe
 * @var string $title Titre de l'item
 * @var string $className='' Attribut html class
 * 
 * @return string (Composant html)
 */
{
    $className = '';
    if (str_contains($link, $_SERVER['SCRIPT_NAME'])) {
        $className .= 'active';
    }

    $link = htmlspecialchars($link);

    $html = <<<HTML
    <li class="$className">
        <a href="$link">$title</a>
    </li>
HTML;

    return $html;
}

function navMenu(Logged $logged): string
/**
 * Menu de navigation
 * 
 * @var Logged $logged
 * 
 * @return string (Composant html)
 */
{
    $items = (
        navItem('/index.php', 'Accueil') .
        navItem('/recherche.php', 'Recherches')
    );

    if ($logged->is()) {
        $user = $logged->user();

        $items .= (
            navItem('/suivis.php', 'Suivis') .
            navItem('/nouveau.php', 'Nouveau') .
            navItem('/compte.php', 'Compte')
        );
    } else {
        $items .= (
            navItem('/connexion.php', 'Se connecter')
        );
    }

    $html = <<<HTML
    <nav>
        <ul>
            $items
        </ul>
    </nav>
HTML;

    return $html;
}
