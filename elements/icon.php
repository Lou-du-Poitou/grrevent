<?php

function icon(string $iconName): string
/**
 * Icone FontAwesome
 * 
 * @var string $iconName
 * 
 * @return string (Composant html)
 */
{
    $iconName = htmlspecialchars($iconName);
    
    $html = <<<HTML
    <i class="fas fa-{$iconName}"></i>
HTML;

    return $html;
}
