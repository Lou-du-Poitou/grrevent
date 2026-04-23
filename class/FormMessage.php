<?php

class FormMessage
/**
 * Contient les différentes message de réponse des formulaires
 */
{
    /**
     * Par convention : en anglais
     * public static $TYPE = [
     *  'keyName' => 'Message';
     * ];
     */

    // ERROR

    private static $ERRORS = array(
        // Syntaxe
        'ValidationRegexFail' => "Les données envoyés ne respecte pas le format demandé", 

        // Authentification
        'DuplicateEmailPseudo' => "Email ou pseudo déjà pris", 
        'Login' => "Identifiant ou mot de passe invalide", 
        'InvalidEmail' => "Email incorrect ou inconnu", 
        'PasswordNotSame' => "Les mots de passe ne correspondent pas", 
        'InvalidToken' => "Token invalide ou expiré", 

        // Événement ou Utilisateur
        'InvalidDate' => "Cette date n'est pas valide", 
        'TooLongTitle' => "Votre titre est trop long", 
        'TooLongDescription' => "Votre description est trop longue", 
        'TooLongLocation' => "Votre localisation est trop longue", 
        'MaxSizeFile' => "Votre fichier est trop lourd", 
        'InvalidFormat' => "Ce format n'est pas pris en charge",
        'TooLongName' => "Ce nom d'utilisateur est trop long",
        'TooLongBiography' => "Cette biography est trop longue",

        // Base de données
        'DataBase' => "Erreur de la base de données", 

        // Erreur par défaut ou inconnue
        'Default' => "Un erreur s'est produite"
    );

    // SUCCESS

    private static $SUCCESS = array(
        // Authentification
        'EmailSent' => "Un mail a été envoyé", 
        'PasswordUpdated' => "Votre mot de passe a été changé", 

        // Succès par défaut
        'Default' => "Succès de l'opération"
    );

    // UPLOAD
    private static $UPLOAD = [
        UPLOAD_ERR_OK => "Fichier téléchargé avec succès",
        UPLOAD_ERR_INI_SIZE => "Le fichier est trop lourd",
        UPLOAD_ERR_FORM_SIZE => "Le fichier est trop lourd",
        UPLOAD_ERR_PARTIAL => "Le fichier n'a été que partiellement téléchargé",
        UPLOAD_ERR_NO_FILE => "Aucun fichier n'a été téléchargé",
        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant sur le serveur",
        UPLOAD_ERR_CANT_WRITE => "L'enregistrement du fichier a échoué",
        UPLOAD_ERR_EXTENSION => "Le téléchargement de fichiers est interdit",
    ];

    private static function get(string $type, string $key)
    /**
     * Permet d'obtenir un message de formulaire en fonction
     * 
     * @var string $type
     * @var string $key
     * 
     * @return string
     */
    {
        if (isset(self::$$type[$key])) {
            $message = self::$$type[$key];
        } else {
            throw new Exception('message introuvable');
        }

        return $message;
    }

    public static function getError(string $key) {
        return self::get('ERRORS', $key);
    }

    public static function getSuccess(string $key) {
        return self::get('SUCCESS', $key);
    }

    public static function getUpload(string $key) {
        return self::get('UPLOAD', $key);
    }
}
