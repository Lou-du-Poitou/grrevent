<?php

class Session
/**
 * Permet de gérer la session d'un utilisateur
 */
{
    public static function init()
    /**
     * Permet de démarrer une session s'il 
     * n'y en a pas
     * 
     * @return void
     */
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuration des Cookies de Session
            session_set_cookie_params([
                'lifetime' => 0,
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }
    }

    public static function clear()
    /**
     * Permet de détruire la session en 
     * cours
     * 
     * @return void
     */
    {
        self::init();
        
        session_unset();
        session_destroy();
    }
}