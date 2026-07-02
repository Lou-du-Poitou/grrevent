<?php
require_once __DIR__ . '/../config/connection.php';

require_once __DIR__ . '/../class/models/User.php';

require_once __DIR__ . '/../elements/error.exit.php';

function selectUser(
    PDO $db, 
    string $pseudo
): User | bool
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

function isFollowUser(
    PDO $db, 
    int $loggedId, 
    int $userId
): bool
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

function followUser(
    PDO $db, 
    int $loggedId, 
    int $userId
): bool
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

function unfollowUser(
    PDO $db, 
    int $loggedId, 
    int $userId
): bool
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

function selectFollowedUsers(
    PDO $db, 
    int $userId, 
    int $limit, 
    int $offset
): array
/**
 * Permet de sélectionner les suivis d'un utilisateur
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $userId
 * @var int $limit Nombre d'utilisateurs à sélectionner
 * @var int $offset À partir duquel on les sélectionnes
 * 
 * @return array[User]
 */
{
    $users = [];

    try {
        $sql = <<<'SQL'
        SELECT userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt
        FROM UserFollowUser
        INNER JOIN User
        ON User.userId = UserFollowUser.followedId
        WHERE followerId = :user
        LIMIT :limit
        OFFSET :offset;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':user', $userId, PDO::PARAM_INT);
        $state->bindParam(':limit', $limit, PDO::PARAM_INT);
        $state->bindParam(':offset', $offset, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $user) {
            $users[] = new User($user);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $users;
}

function selectSearchedUser(
    PDO $db, 
    string $query, 
    int $limit, 
    int $offset
): array
/**
 * Permet de sélectionner les utilisateurs recherchés
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $query
 * @var int $limit Nombre d'utilisateurs à sélectionner
 * @var int $offset À partir duquel on les sélectionnes
 * 
 * @return array[User]
 */
{
    $users = [];

    try {
        $sql = <<<'SQL'
        SELECT userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt,
            LEAST(MATCH (userPseudo) AGAINST (
                :query IN NATURAL LANGUAGE MODE
            ), 1000) AS relevance1,
            LEAST(MATCH (userName) AGAINST (
                :query IN NATURAL LANGUAGE MODE
            ), 1000) AS relevance2,
            LEAST(MATCH (userBiography) AGAINST (
                :query IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION
            ), 1000) AS relevance3,
            LEAST(MATCH (userLocation) AGAINST (
                :query IN NATURAL LANGUAGE MODE
            ), 1000) AS relevance4
        FROM User
        WHERE MATCH (userPseudo) AGAINST (
            :query IN NATURAL LANGUAGE MODE
        )
        OR MATCH (userName) AGAINST (
            :query IN NATURAL LANGUAGE MODE
        )
        OR MATCH (userBiography) AGAINST (
            :query IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION
        )
        OR MATCH (userLocation) AGAINST (
            :query IN NATURAL LANGUAGE MODE
        )
        -- Technique astucieuse pour définir les priorités de la correspondance
        ORDER BY (
            (relevance1 * 2) + 
            (relevance2 * 1.5) + 
            (relevance3 * 1) + 
            (relevance4 * 1.5)
        ) DESC
        LIMIT :limit
        OFFSET :offset;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':query', $query);
        $state->bindParam(':limit', $limit, PDO::PARAM_INT);
        $state->bindParam(':offset', $offset, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $user) {
            $users[] = new User($user);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $users;
}

function updateUser(
    PDO $db, 
    int $id,
    string | null $name, 
    string | null $biography, 
    string | null $picture, 
    string | null $location
): User | null
/**
 * Permet de modifier les informations d'un utilisateur
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string | null $name
 * @var string | null $biography
 * @var string | null $picture
 * @var string | null $location
 * 
 * @return User
 * @return null
 */
{
    $user = null;

    try {
        $updateSql = <<<SQL
        UPDATE User SET
            userName = :name,
            userBiography = :biography,
            userPicture = :picture,
            userLocation = :location
        WHERE userId = :id;
SQL;

        $state = $db->prepare($updateSql);

        $state->bindParam(':name', $name);
        $state->bindParam(':biography', $biography);
        $state->bindParam(':picture', $picture);
        $state->bindParam(':location', $location);
        $state->bindParam(':id', $id, PDO::PARAM_INT);

        $state->execute();
        $state->closeCursor();

        $selectSql = <<<SQL
        SELECT userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt
        FROM User
        WHERE userId = :id;
SQL;

        $state = $db->prepare($selectSql);

        $state->bindParam(':id', $id, PDO::PARAM_INT);

        $state->execute();

        $state->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $state->fetch();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $user;
}
