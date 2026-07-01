<?php

function select(
    array $options, 
    array $get
): string
/**
 * Permet de générer un menu de sélection
 * 
 * @var array $options
 * @var array $get
 * 
 * @return string (Composant HTML)
 */
{
    $optionsHTML = '';

    foreach ($options as $value => $title) {
        $title = htmlspecialchars($title);
        $value = htmlspecialchars($value);

        $selected = null;
        if (in_array($value , $get)) {
            $selected = 'selected';
        }

        $optionsHTML .= <<<HTML
        <option value="$value" $selected>$title</option>
HTML;
    }


    $html = <<<HTML
    <select name="type">
        $optionsHTML
    </select>
HTML;

    return $html;
}
