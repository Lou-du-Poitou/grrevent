<?php
require_once __DIR__ . '/Session.php';

class CSRFToken
/**
 * Permet de gérer les tokens CSRF
 */
{
    private const KEY = 'csrf_token';

    public function __construct()
    {
        Session::init();

        if (empty($this->get())) {
            $this->new();
        }
    }

    public function get(): string | null
    /**
     * Renvoie le token CSRF ou null
     * 
     * @return string
     * @return null
     */
    {
        return $_SESSION[self::KEY] ?? null;
    }

    private function new(): void
    /**
     * Génère une nouvelle valeur pour le 
     * token
     * 
     * @return void
     */
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::KEY] = $token;
    }
}