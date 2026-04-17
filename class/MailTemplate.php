<?php

class MailTemplate
/**
 * Contient les différents templates html pour l'envois des mails
 */
{
    private static function makeTemplate(string $title, string $html): string
    /**
     * Créer le template html final
     * 
     * @var string $title Titre à insérer
     * @var string $html Composant html à insérer
     * 
     * @return string (Composant html pour mail) 
     */
    {
        $template = <<<HTML
        <html>
        <head>
            <title>$title</title>
            <style>
                body {
                    font-family: Arial sans-serif;
                }

                h1, h2, h3, h4, h5, h6 {
                    font-size: 1rem;
                    font-weight: 500;
                }

                a {
                    color: blue;
                    text-decoration: none;
                }

                a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            $html
        </body>
        </html>
HTML;

        return $template;
    }
    
    // DIFFÉRENTS TEMPLATES //

    public static function requestResetPassword(string $pseudo, string $link): string
    /**
     * Template pour la demande de réinitialisation de mot de passe
     * 
     * @var string $pseudo
     * @var string $link Lien pour réinitiliser
     * 
     * @return string (Composant html pour mail)
     */
    {
        $title = 'Réinitialiser mot de passe';
        $pseudo = htmlspecialchars($pseudo);

        $html = <<<HTML
        <h1>Hey $pseudo</h1>
        <p>
            Vous avez demandé à réinitialiser votre mot de passe
            <br>
            Pour le changer: 
            <a href="$link">Cliquez-ici</a>
        </p>
        <div>
            <p>
                Vous n'êtes pas à l'origine de la demande ? 
                <i>Ignorez ce mail</i>
            </p>
        </div>
HTML;

        return self::makeTemplate($title, $html);
    }
}
