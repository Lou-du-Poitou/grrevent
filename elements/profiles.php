<?php
require_once './class/User.php';
require_once './class/Event.php';
require_once './class/Logged.php';

function backButton(): string
/**
 * Renvoie le bouton pour revenir à la page précédente
 * 
 * @return string (Composant HTML)
 */
{
    $html = <<<HTML
    <div class="back-btn">
        <button onclick="history.go(-1)">Retour</button>
    </div>
HTML;

    return $html;
}

function profileHeader(string $keyword, string $value, ?string $link=null): string
/**
 * Renvoie un élément header d'un profil
 * 
 * @var string $keyword
 * @var string $value
 * @var ?string $link=null
 * 
 * @return string (Composant HTML)
 */
{
    $set = htmlspecialchars($value);
    if ($link) {
        $set = <<<HTML
        <a href="$link">$set</a>
HTML;
    }

    $html = <<<HTML
    <p>
        <b>$keyword:</b> $set
    </p>
HTML;

    return $html;
}
