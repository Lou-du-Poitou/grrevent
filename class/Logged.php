<?php
require_once './class/User.php';

class Logged
/**
 * Permet de gérer les informations de l'utilisateur connecté
 */
{
    private User | null $user = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
        }
    }

    public function user(): User | null
    /**
     * Renvoie l'utilisateur connecté
     * 
     * @return User
     * @return null
     */
    {
        return $this->user;
    }

    public function is(): bool
    /**
     * Renvoie si un utilisateur est connecté ou non
     * 
     * @return bool
     */
    {
        return !empty($this->user);
    }

    public static function setUser(User $user): void
    /**
     * Permet de mettre l'utilisateur connecté
     * 
     * @param User $user
     * 
     * @return void
     */
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user'] = $user;
    }
}
