<?php
require_once './class/Logged.php';

function navItem(string $link, string $title, string $className=''): string
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
        navItem('/', 'Accueil')/*.
        navItem('', 'Autre').
        navItem('', 'Autre')*/
    );

    if ($logged->is()) {
        $user = $logged->user();

        $items .= (
            navItem(HostUrl::pathToUser($user->getValue('userPseudo')), 'Mon profil') .
            navItem('/deconnexion.php', 'Déconnexion')
        );
    } else {
        $items .= (
            navItem('/connexion.php', 'Se connecter') /*.
            navItem('/inscription.php', "S'inscrire")*/
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
