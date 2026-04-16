<?php
require './class/Entity.php';

class User extends Entity 
/**
 * Utilisateur du site (compte créé)
 */
{
    protected int $userId;
    protected string $userPseudo;
    protected string | null $userName;
    protected string | null $userBiography;
    protected string | null $userPicture;
}
