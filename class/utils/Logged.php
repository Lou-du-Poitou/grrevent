<?php
require_once './class/utils/Session.php';
require_once './class/models/User.php';

class Logged
/**
 * Permet de gérer les informations de l'utilisateur connecté
 */
{
    private const KEY = 'user';

    public function __construct()
    {
        Session::init();
    }

    public function user(): User | null
    /**
     * Renvoie l'utilisateur connecté
     * 
     * @return User
     * @return null
     */
    {
        return $_SESSION[self::KEY] ?? null;
    }

    public function is(): bool
    /**
     * Renvoie si un utilisateur est connecté ou non
     * 
     * @return bool
     */
    {
        return !empty($this->user());
    }

    public function setUser(User $user): void
    /**
     * Permet de mettre l'utilisateur connecté
     * 
     * @var User $user
     * 
     * @return void
     */
    {
        $_SESSION[self::KEY] = $user;
    }
}
