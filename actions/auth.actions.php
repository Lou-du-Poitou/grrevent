<?php
require_once './config/connection.php';
require_once './class/User.php';

function register(PDO $db, string $pseudo, string $email, string $password): User | null
/**
 * Permet d'inscrire un nouvel utilisateur dans la base de données
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $pseudo
 * @var string $email
 * @var string $password
 * 
 * @return User L'utilisateur inséré si tout s'est bien passé
 * @return null En cas d'erreur
 */
{
    $password = password_hash($password, PASSWORD_DEFAULT);

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

        $state->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $state->bindParam(':email', $email, PDO::PARAM_STR);
        $state->bindParam(':password', $password, PDO::PARAM_STR);

        $state->execute();
        $state->closeCursor();

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
        $state->bindParam(':id', $userId);
        $state->execute();

        $state->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $state->fetch();
    } catch (PDOException $err) {
        return null;
    }
    
    return $user;
}

function duplicateEmailPseudo(PDO $db, string $pseudo, string $email): bool | null
/**
 * Vérifie si le pseudo ou l'email est déjà utilisé
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $pseudo
 * @var string $email
 * 
 * @return bool
 * true: Si pseudo/email déjà pris
 * false: Si pseudo/email libre
 * @return null En cas d'erreur
 */
{
    $duplicate = false;

    try {
        $sql = <<<'SQL'
        SELECT COUNT(*) AS duplicate
        FROM User
        WHERE userPseudo = :pseudo
        OR userEmail = :email
        LIMIT 1;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':pseudo', $pseudo);
        $state->bindParam(':email', $email);

        $state->execute();
        $state->setFetchMode(PDO::FETCH_ASSOC);

        $data = $state->fetch();
        $duplicate = (bool)$data['duplicate'];
    } catch (PDOException $err) {
        return null;
    }

    return $duplicate;
}

function login(PDO $db ,string $email, string $password): User | null
/**
 * Renvoie l'utilisateur qui essaye de se connecter
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $email
 * @var string $password
 * 
 * @return User Connexion réussi
 * @return null Si id/mdp invalide
 */
{
    try {
        $sql = <<<'SQL'
        SELECT userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userPassword
        FROM User
        WHERE userEmail = :email;
SQL;

        $state = $db->prepare($sql);
        $state->bindParam(':email', $email);
        $state->execute();

        $data = $state->fetch(PDO::FETCH_ASSOC);
        $user = null;
        if (isset($data['userPassword']) && password_verify($password, $data['userPassword'])) {
            $user = new User($data);
        }
    } catch (PDOException $err) {
        return null;
    }

    return $user;
}
