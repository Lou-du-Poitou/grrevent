<?php
require_once __DIR__ . '/../config/constants.php';

function errorExit(string $message, ?string $err=null): void
/**
 * Affiche un message et quitte le script
 * 
 * @var string $message
 * @var ?string $err=null
 * 
 * @return void (Affichage HTML)
 */
{
    // Ne rien afficher en production
    if (PRODUCTION_ENV) die();

    $message = htmlspecialchars($message);
    $err = htmlspecialchars($err);
    
    $html = <<<HTML
    <b><p>$message:</p></b>
    <pre>$err</pre>
HTML;

    echo $html;
    exit();
}
