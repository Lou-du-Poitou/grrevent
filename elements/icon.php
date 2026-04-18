<?php

function icon(string $iconName)
/**
 * Icone FontAwesome
 * 
 * @var string $iconName
 * 
 * @return string (Composant html)
 */
{
    $html = <<<HTML
    <i class="fas fa-{$iconName}"></i>
HTML;

    return $html;
}
