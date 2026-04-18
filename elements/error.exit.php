<?php

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
    $html = <<<HTML
    <b><p>$message:</p></b>
    <pre>$err</pre>
HTML;

    echo $html;
    exit();
}
