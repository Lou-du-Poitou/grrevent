<?php

class FormMessage
/**
 * Contient les différentes message de réponse des formulaires
 */
{
    /**
     * Par convention : en anglais
     * public static $TYPEPascalCase = 'Message';
     */

    // ERROR

    // Syntaxe
    private static string $ERRORValidationRegexFail = "Les données envoyés ne respecte pas le format demandé";

    // Authentification
    private static string $ERRORDuplicateEmailPseudo = "Email ou pseudo déjà pris";
    private static string $ERRORLogin = "Identifiant ou mot de passe invalide";
    private static string $ERRORInvalidEmail = "Email incorrect ou inconnu";
    private static string $ERRORPasswordNotSame = "Les mots de passe ne correspondent pas";
    private static string $ERRORInvalidToken = "Token invalide ou expiré";
    
    // Base de données
    private static string $ERRORDataBase = "Erreur de la base de données";
    
    

    private static string $ERROR = "Un erreur s'est produite";

    // SUCCESS

    // Authentification
    private static string $SUCCESSEmailSent = "Un mail a été envoyé";
    private static string $SUCCESSPasswordUpdated = "Votre mot de passe a été changé";

    private static string $SUCCES = "Succès de l'opération";

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
        $key = $type . $key;
        if (isset(self::$$key)) {
            $message = self::$$key;
        } else {
            throw new Exception('message introuvable');
        }

        return $message;
    }

    public static function getError(string $key) {
        return self::get('ERROR', $key);
    }

    public static function getSuccess(string $key) {
        return self::get('SUCCESS', $key);
    }
}
