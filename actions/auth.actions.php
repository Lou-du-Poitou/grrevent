<?php
require_once './config/connection.php';

require_once './class/User.php';
require_once './class/Mail.php';
require_once './class/MailTemplate.php';
require_once './class/HostUrl.php';

require_once './elements/error.exit.php';

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
 * @return null
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

        $state->bindParam(':pseudo', $pseudo);
        $state->bindParam(':email', $email);
        $state->bindParam(':password', $password);

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
        $state->bindParam(':id', $userId, PDO::PARAM_INT);
        $state->execute();

        $state->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $state->fetch();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }
    
    return $user;
}

function duplicateEmailPseudo(PDO $db, string $pseudo, string $email): bool
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
        errorExit(DB_ERROR_MESSAGE, $err);
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
    $user = null;

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
        if (isset($data['userPassword']) && password_verify($password, $data['userPassword'])) {
            $user = new User($data);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $user;
}

function requestResetPassword(PDO $db, string $email): bool
/**
 * Permet de faire suivre la demande de réinitialisation de mot de passe
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $email
 * 
 * @return
 */
{
    try {
        $selectSql = <<<'SQL'
        SELECT userId,
            userPseudo
        FROM User
        WHERE userEmail = :email;
SQL;

        $state = $db->prepare($selectSql);
        $state->bindParam(':email', $email);
        $state->execute();

        $data = $state->fetch(PDO::FETCH_ASSOC);
        $state->closeCursor();

        if (isset($data['userId']) && isset($data['userPseudo'])) {
            $insertSql = <<<'SQL'
            INSERT INTO Token (
                userId,
                tokenValue
            ) VALUES (
                :userId,
                :tokenHash
            ) ON DUPLICATE KEY UPDATE
            tokenValue = :tokenHash,
            tokenExpires = (NOW() + INTERVAL 48 HOUR);
SQL;
            $userId = $data['userId'];
            $userPseudo = $data['userPseudo'];

            // On génère un token qu'on envera par mail, et le hashage qu'on stockera
            $token = bin2hex(random_bytes(32));
            $tokenHash = password_hash($token, PASSWORD_DEFAULT);

            $state = $db->prepare($insertSql);
            $state->bindParam('userId', $userId, PDO::PARAM_INT);
            $state->bindParam('tokenHash', $tokenHash);

            $state->execute();

            $link = HostUrl::path("/motpasse.php?token=$token&id=$userId"); // Lien pour réinitialiser le mot de passe
            $template = MailTemplate::requestResetPassword($userPseudo, $link);

            $mail = new Mail($email, true);
            $mail->setSubject('Réinitialisation mot de passe');
            $mail->setMessage($template);
            $mail->send();
        } else {
            return false;
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return true;
}

function resetPassword(PDO $db, int $id, string $token, string $password)
/**
 * Permet de faire suivre la demande de réinitialisation de mot de passe
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $id
 * @var string $password
 * 
 * @return
 */
{
    $password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $selectSql = <<<'SQL'
        SELECT tokenValue
        FROM Token
        WHERE userId = :id
        AND tokenExpires > NOW();
SQL;

        $state = $db->prepare($selectSql);
        $state->bindParam(':id', $id, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetch(PDO::FETCH_ASSOC);
        $state->closeCursor();

        // Vérification de la validité du token
        if (isset($data['tokenValue']) && password_verify($token, $data['tokenValue'])) {
            $updateSql = <<<'SQL'
            UPDATE User
            SET userPassword = :password
            WHERE userId = :id;
            DELETE FROM Token
            WHERE userId = :id;
SQL;

            $state = $db->prepare($updateSql);
            $state->bindParam(':password', $password);
            $state->bindParam(':id', $id);

            $state->execute();
            $state->closeCursor();
        } else {
            return false;
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return true;
}
