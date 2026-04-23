<?php
require_once './config/constants.php';

require_once './elements/icon.php';

function textInput(
    string $name, 
    string $placeholder='', 
    string | null $value='', 
    ?bool $required=true, 
): string
/**
 * Champ de texte classique
 * 
 * @var string $name Attribut html name
 * @var string $placeholder='' Attribut html placeholder
 * @var string | null $value='' Attribut html value
 * @var ?bool $required=true Champ requis ou non
 * @var ?string $pattern=''
 * 
 * @return string (Composant html)
 */
{
    $name = htmlspecialchars($name);
    $placeholder = htmlspecialchars($placeholder);
    $value = htmlspecialchars((string)$value);

    $required = $required ? 'required' : '';

    $html = <<<HTML
    <input type="text"
        name="$name"
        placeholder="$placeholder"
        title="Champ de texte"
        value="$value"
        $required
    >
HTML;

    return $html;
}

function passwordInput(
    string $name, 
    string $placeholder=''
): string
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

    $name = htmlspecialchars($name);
    $placeholder = htmlspecialchars($placeholder);

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

function emailInput(
    string $name, 
    string $placeholder='', 
    string $value=''
): string
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

    $name = htmlspecialchars($name);
    $placeholder = htmlspecialchars($placeholder);
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

function pseudoInput(
    string $name, 
    string $placeholder='', 
    string $value=''
): string
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

    $name = htmlspecialchars($name);
    $placeholder = htmlspecialchars($placeholder);
    $value = htmlspecialchars($value);

    $html = <<<HTML
    <input type="text"
        name="$name"
        placeholder="$placeholder"
        pattern="$regex"
        title="3 caractères minimum, lettres minuscules, chiffres et _"
        value="$value"
        required
    >
HTML;

    return $html;
}

function buttonInput(
    string $title, 
    string $type='button', 
    bool $disabled=false, 
    string $iconName=''
): string
/**
 * Bouton classique
 * 
 * @var string $title Titre et valeur du bouton
 * @var string $type='button' Type du bouton
 * @var bool $disabled=false
 * @var string $iconName=''
 * 
 * @return string (Composant html)
 */
{
    $disabled = $disabled ? 'disabled' : '';

    $title = htmlspecialchars($title);

    $icon = null;
    if ($iconName) {
        $icon = icon($iconName) . ' ';
    }

    $html = <<<HTML
    <button type="$type"  
        title="$title"
        $disabled
    >$icon$title</button>
HTML;

    return $html;
}

function searchInput(
    string $name, 
    string $placeholder='', 
    string $value=''
): string
/**
 * Champ de texte pour les recherches
 * 
 * @var string $name Attribut html name
 * @var string $placeholder='' Attribut html placeholder
 * @var string $value='' Attribut html value
 * 
 * @return string (Composant html)
 */
{
    $name = htmlspecialchars($name);
    $placeholder = htmlspecialchars($placeholder);
    $value = htmlspecialchars($value);

    $html = <<<HTML
    <input type="search"
        name="$name"
        placeholder="$placeholder"
        title="200 caractères maximum"
        value="$value"
        maxlength="200"
        required
    >
HTML;

    return $html;
}

function textareaInput(
    string $name, 
    string $placeholder='', 
    string | null $value='', 
    ?bool $required=true, 
): string
/**
 * Champ de texte textarea
 * 
 * @var string $name Attribut html name
 * @var string $placeholder='' Attribut html placeholder
 * @var string $value='' Attribut html value
 * @var ?bool $required=true Champ requis ou non
 * 
 * @return string (Composant html)
 */
{
    $name = htmlspecialchars($name);
    $placeholder = htmlspecialchars($placeholder);
    $value = htmlspecialchars((string)$value);

    $required = $required ? 'required' : '';

    $html = <<<HTML
    <textarea
        name="$name"
        placeholder="$placeholder"
        title="Champ de texte"
        $required
    >$value</textarea>
HTML;

    return $html;
}

function datetimeInput(
    string $name, 
    string $value='', 
    ?bool $required=true
): string
/**
 * Champ d'entrée pour les dates et horaires
 * 
 * @var string $name Attribut html name
 * @var string $value='' Attribut html value
 * @var ?bool $required=true Champ requis ou non
 * 
 * @return string (Composant html)
 */
{
    $name = htmlspecialchars($name);
    $value = htmlspecialchars($value);

    $required = $required ? 'required' : '';

    $html = <<<HTML
    <input type="datetime-local"
        name="$name"
        title="Date et horaire"
        value="$value"
        $required
    >
HTML;

    return $html;
}

function numberInput(
    string $name, 
    string $placeholder='', 
    ?int $value=null, 
    ?int $min=0, 
    ?bool $required=true
): string
/**
 * Champ de texte classique
 * 
 * @var string $name Attribut html name
 * @var string $placeholder='' Attribut html placeholder
 * @var string= $value='' Attribut html value
 * @var ?int $min=0 Valeur minimal
 * @var ?bool $required=true Champ requis ou non
 * 
 * @return string (Composant html)
 */
{
    $name = htmlspecialchars($name);
    $placeholder = htmlspecialchars($placeholder);
    $value = htmlspecialchars((string)$value);

    $required = $required ? 'required' : '';

    $html = <<<HTML
    <input type="number"
        name="$name"
        placeholder="$placeholder"
        title="Nombre"
        value="$value"
        min="$min"
        $required
    >
HTML;

    return $html;
}

function fileInput(
    string $name, 
    string $accept='', 
    ?bool $required=true
): string
/**
 * Champ de texte classique
 * 
 * @var string $name Attribut html name
 * @var string $accept='' Attribut html accept
 * @var ?bool $required=true Champ requis ou non
 * 
 * @return string (Composant html)
 */
{
    $name = htmlspecialchars($name);
    $accept = htmlspecialchars($accept);

    $required = $required ? 'required' : '';

    $html = <<<HTML
    <input type="file"
        name="$name"
        title="Charger un fichier"
        accept="$accept"
        $required
    >
HTML;

    return $html;
}
