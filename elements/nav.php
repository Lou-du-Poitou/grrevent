<?php

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
    if ($_SERVER['SCRIPT_NAME'] === $link) {
        $className .= 'active';
    }

    $html = <<<HTML
    <li class="$className">
        <a href="$link">$title</a>
    </li>
HTML;

    return $html;
}

function navMenu(): string
/**
 * Menu de navigation
 * 
 * @return string (Composant html)
 */
{
    $items = (
        navItem('/', 'Accueil').
        navItem('', 'Autre').
        navItem('', 'Autre')
    );

    $html = <<<HTML
    <nav>
        <ul>
            $items
        </ul>
    </nav>
HTML;

    return $html;
}
