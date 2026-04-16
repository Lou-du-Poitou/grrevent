<?php
require_once './config/regex.php';

function passwordInput(string $name, string $placeholder=''): string
/**
 * Champ de texte pour mot de passe
 * 
 * @var string $name Attribut html name
 * @var string $placeholder='' Attribut html placeholder
 * 
 * @return string (Composant html)
 */
{
    $regex = str_replace('/', '', PASSWORD_REGEX);

    $html = <<<HTML
    <input type="password"
        name="$name"
        pattern="$regex" 
        title="8 caractères minimum, une lettre et un chiffre"
        placeholder="$placeholder"
        required
    >
HTML;

    return $html;
}

function emailInput(string $name, string $placeholder='', string $value=''): string
/**
 * Champ de texte pour email
 * 
 * @var string $name Attribut html name
 * @var string $placeholder='' Attribut html placeholder
 * @var string $value='' Attribut html value
 * 
 * @return string (Composant html)
 */
{
    $regex = str_replace('/', '', EMAIL_REGEX);

    $value = htmlspecialchars($value);

    $html = <<<HTML
    <input type="email"
        name="$name"
        placeholder="$placeholder"
        pattern="$regex"
        value="$value"
        required
    >
HTML;

    return $html;
}

function pseudoInput(string $name, string $placeholder='', string $value=''): string
/**
 * Champ de texte pour pseudo
 * 
 * @var string $name Attribut html name
 * @var string $placeholder='' Attribut html placeholder
 * @var string $value='' Attribut html value
 * 
 * @return string (Composant html)
 */
{
    $regex = str_replace('/', '', PSEUDO_REGEX);

    $value = htmlspecialchars($value);

    $html = <<<HTML
    <input type="text"
        name="$name"
        placeholder="$placeholder"
        pattern="$regex"
        value="$value"
        required
    >
HTML;

    return $html;
}
