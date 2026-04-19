<?php
require_once './config/connection.php';

require_once './class/User.php';

require_once './elements/error.exit.php';

function selectUser(PDO $db, string $pseudo): User | false
/**
 * Permet de sélectionner un utilisateur grâce à son pseudo
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $pseudo
 * 
 * @return User
 * @return false
 */
{
    try {
        $sql = <<<'SQL'
        SELECT userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt
        FROM User
        WHERE userPseudo = :pseudo;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':pseudo', $pseudo);

        $state->execute();

        $state->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $state->fetch();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $user;
}

function isFollowUser(PDO $db, int $loggedId, int $userId): bool
/**
 * Vérifie si un utilisateur connecté en suit un autre
 * 
 * @var PDO $db
 * @var int $loggedId
 * @var int $userId
 * 
 * @return bool
 */
{
    try {
        $sql = <<<'SQL'
        SELECT count(*) AS isFollow
        FROM UserFollowUser
        WHERE followerId = :logged
        AND followedId = :user;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':logged', $loggedId, PDO::PARAM_INT);
        $state->bindParam(':user', $userId, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $isFollow = $data['isFollow'];
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $isFollow;
}

function followUser(PDO $db, int $loggedId, int $userId): true
/**
 * Permet à un utilisateur d'en suivre un autre
 * 
 * @var PDO $db
 * @var int $loggedId
 * @var int $userId
 * 
 * @return true
 */
{
    try {
        $sql = <<<'SQL'
        INSERT IGNORE INTO UserFollowUser (
            followerId,
            followedId
        ) VALUES (
            :logged,
            :user
        );
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':logged', $loggedId, PDO::PARAM_INT);
        $state->bindParam(':user', $userId, PDO::PARAM_INT);

        $state->execute();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return true;
}

function unfollowUser(PDO $db, int $loggedId, int $userId): true
/**
 * Permet à un utilisateur de ne plus en suivre un autre
 * 
 * @var PDO $db
 * @var int $loggedId
 * @var int $userId
 * 
 * @return true
 */
{
    try {
        $sql = <<<'SQL'
        DELETE FROM UserFollowUser
        WHERE followerId = :logged
        AND followedId = :user;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':logged', $loggedId, PDO::PARAM_INT);
        $state->bindParam(':user', $userId, PDO::PARAM_INT);

        $state->execute();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return true;
}
