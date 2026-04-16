<?php
require_once './config/connection.php';
require_once './class/User.php';

function register(PDO $db, string $pseudo, string $email, string $password): User | bool
/**
 * Permet d'inscrire un nouvel utilisateur dans la base de données
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $pseudo
 * @var string $email
 * @var string $password
 * 
 * @return User L'utilisateur inséré si tout s'est bien passé
 * @return false En cas d'erreur
 */
{
    $password = password_hash($password, '2y');

    try {
        // Insertion du nouvel utilisateur
        $insertSql = <<<'SQL'
        INSERT INTO User (
            userPseudo,
            userEmail,
            userPassword
        ) VALUES (
            :pseudo,
            :email,
            :password
        );
SQL;

        $state = $db->prepare($insertSql);

        $state->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
        $state->bindParam(":email", $email, PDO::PARAM_STR);
        $state->bindParam(":password", $password, PDO::PARAM_STR);

        $state->execute();

        // Selection du nouvel utilisateur
        $selectSql = <<<'SQL'
        SELECT userId,
            userPseudo,
            userName,
            userBiography,
            userPicture
        FROM User 
        WHERE userId = :id;
SQL;
        $userId = $db->lastInsertId();

        $state = $db->prepare($selectSql);
        $state->bindParam(":id", $userId);
        $state->execute();

        $state->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $state->fetch();
    } catch (PDOException $err) {
        return false;
    }
    
    return $user;
}

function duplicateEmail() {}
