<?php
require_once './config/connection.php';

require_once './class/User.php';
require_once './class/Event.php';

require_once './elements/error.exit.php';

function selectEvent(
    PDO $db, 
    int $id
): Event | bool
/**
 * Permet de sélectionner un événement grâce à son identifiant
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $id
 * 
 * @return Event
 * @return false
 */
{
    $event = false;

    try {
        $sql = <<<'SQL'
        SELECT eventId,
            eventTitle,
            eventDescription,
            eventDate,
            eventLocation,
            eventPlaces,
            eventPicture,
            User.userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt
        FROM Event
        NATURAL JOIN User
        WHERE eventId = :id;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':id', $id, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetch(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $author = new User($data);
            $data['author'] = $author;
            $event = new Event($data);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $event;
}

function isAddedEvent(
    PDO $db, 
    int $loggedId, 
    int $eventId
): bool
/**
 * Vérifie si un utilisateur connecté a ajouté un événement
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $loggedId
 * @var int $eventId
 * 
 * @return bool
 */
{
    try {
        $sql = <<<'SQL'
        SELECT count(*) AS isAdded
        FROM UserAddEvent
        WHERE userId = :logged
        AND eventId = :event;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':logged', $loggedId, PDO::PARAM_INT);
        $state->bindParam(':event', $eventId, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $isAdded = $data['isAdded'];
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $isAdded;
}

function addEvent(
    PDO $db, 
    int $loggedId, 
    int $eventId
): bool
/**
 * Permet à un utilisateur d'ajouter un événement
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $loggedId
 * @var int $eventId
 * 
 * @return true
 */
{
    try {
        $sql = <<<'SQL'
        INSERT IGNORE INTO UserAddEvent (
            userId,
            eventId
        ) VALUES (
            :logged,
            :event
        );
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':logged', $loggedId, PDO::PARAM_INT);
        $state->bindParam(':event', $eventId, PDO::PARAM_INT);

        $state->execute();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return true;
}

function removeEvent(
    PDO $db, 
    int $loggedId, 
    int $eventId
): bool
/**
 * Permet à un utilisateur de retirer un événement
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $loggedId
 * @var int $eventId
 * 
 * @return true
 */
{
    try {
        $sql = <<<'SQL'
        DELETE FROM UserAddEvent
        WHERE userId = :logged
        AND eventId = :event;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':logged', $loggedId, PDO::PARAM_INT);
        $state->bindParam(':event', $eventId, PDO::PARAM_INT);

        $state->execute();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return true;
}

function deleteEvent(
    PDO $db, 
    int $eventId
): bool
/**
 * Permet de supprimer un événement
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $eventId
 * 
 * @return true
 */
{
    try {
        $sql = <<<'SQL'
        DELETE FROM Event
        WHERE eventId = :event;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':event', $eventId, PDO::PARAM_INT);

        $state->execute();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return true;
}

function selectUserEvents(
    PDO $db, 
    int $userId, 
    int $limit, 
    int $offset
): array
/**
 * Permet de renvoyer des événements d'un utilisateur
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $userId
 * @var int $limit Nombre d'événements à sélectionner
 * @var int $offset À partir duquel on les sélectionnes
 * 
 * @return array[Event]
 */
{
    $events = [];

    try {
        $sql = <<<'SQL'
        SELECT eventId,
            eventTitle,
            eventDescription,
            eventDate,
            eventLocation,
            eventPlaces,
            eventPicture,
            User.userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt
        FROM Event
        NATURAL JOIN User
        WHERE User.userId = :user
        ORDER BY 
            -- En priorité ceux qui ne sont pas passés par date croissante
            -- Et ceux qui sont passés par date décroisante
            eventDate > NOW() DESC,
            CASE WHEN eventDate > NOW() THEN eventDate END ASC,
            CASE WHEN eventDate < NOW() THEN eventDate END DESC
        LIMIT :limit
        OFFSET :offset;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':user', $userId, PDO::PARAM_INT);
        $state->bindParam(':limit', $limit, PDO::PARAM_INT);
        $state->bindParam(':offset', $offset, PDO::PARAM_INT);

        $state->execute();
        $data = $state->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $event) {
            $user = new User($event);
            $event['author'] = $user;

            $events[] = new Event($event);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $events;
}

function selectAddedEvents(
    PDO $db, 
    int $userId, 
    int $limit, 
    int $offset
): array
/**
 * Permet de sélectionner les événements ajoutés par un utilisateur
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $userId
 * @var int $limit Nombre d'événements à sélectionner
 * @var int $offset À partir duquel on les sélectionnes
 * 
 * @return array[Event]
 */
{
    $events = [];

    try {
        $sql = <<<'SQL'
        SELECT Event.eventId,
            eventTitle,
            eventDescription,
            eventDate,
            eventLocation,
            eventPlaces,
            eventPicture,
            User.userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt
        FROM UserAddEvent
        INNER JOIN Event
            ON Event.eventId = UserAddEvent.eventId
        INNER JOIN User
            ON User.userId = Event.userId
        WHERE UserAddEvent.userId = :user
        ORDER BY 
            -- En priorité ceux qui ne sont pas passés par date croissante
            -- Et ceux qui sont passés par date décroisante
            eventDate > NOW() DESC,
            CASE WHEN eventDate > NOW() THEN eventDate END ASC,
            CASE WHEN eventDate < NOW() THEN eventDate END DESC
        LIMIT :limit
        OFFSET :offset;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':user', $userId, PDO::PARAM_INT);
        $state->bindParam(':limit', $limit, PDO::PARAM_INT);
        $state->bindParam(':offset', $offset, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $event) {
            $user = new User($event);
            $event['author'] = $user;

            $events[] = new Event($event);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $events;
}

function selectFollowedEvents(
    PDO $db, 
    int $userId, 
    int $limit, 
    int $offset
): array
/**
 * Permet de sélectionner les événements ajoutés par un utilisateur
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var int $userId
 * @var int $limit Nombre d'événements à sélectionner
 * @var int $offset À partir duquel on les sélectionnes
 * 
 * @return array[Event]
 */
{
    $events = [];

    try {
        $sql = <<<'SQL'
        SELECT eventId,
            eventTitle,
            eventDescription,
            eventDate,
            eventLocation,
            eventPlaces,
            eventPicture,
            User.userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt
        FROM UserFollowUser
        INNER JOIN User
        ON User.userId = UserFollowUser.followedId
        NATURAL JOIN Event
        WHERE UserFollowUser.followerId = :user
        ORDER BY 
            -- En priorité ceux qui ne sont pas passés par date croissante
            -- Et ceux qui sont passés par date décroisante
            eventDate > NOW() DESC,
            CASE WHEN eventDate > NOW() THEN eventDate END ASC,
            CASE WHEN eventDate < NOW() THEN eventDate END DESC
        LIMIT :limit
        OFFSET :offset;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':user', $userId, PDO::PARAM_INT);
        $state->bindParam(':limit', $limit, PDO::PARAM_INT);
        $state->bindParam(':offset', $offset, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $event) {
            $user = new User($event);
            $event['author'] = $user;

            $events[] = new Event($event);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $events;
}

function selectSearchedEvent(
    PDO $db, 
    string $query, 
    int $limit, 
    int $offset
): array
/**
 * Permet de sélectionner les événements recherchés
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $query
 * @var int $limit Nombre d'événements à sélectionner
 * @var int $offset À partir duquel on les sélectionnes
 * 
 * @return array[Event]
 */
{
    $events = [];

    try {
        $sql = <<<'SQL'
        SELECT eventId,
            eventTitle,
            eventDescription,
            eventDate,
            eventLocation,
            eventPlaces,
            eventPicture,
            User.userId,
            userPseudo,
            userName,
            userBiography,
            userPicture,
            userLocation,
            userJoinedAt,
            LEAST(MATCH (eventTitle) AGAINST (
                :query IN NATURAL LANGUAGE MODE
            ), 1000) AS relevance1,
            LEAST(MATCH (eventDescription) AGAINST (
                :query IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION
            ), 1000) AS relevance2,
            LEAST(MATCH (eventLocation) AGAINST (
                :query IN NATURAL LANGUAGE MODE
            ), 1000) AS relevance3,
            LEAST(MATCH (userPseudo) AGAINST (
                :query IN NATURAL LANGUAGE MODE
            ), 1000) AS relevance4,
            LEAST(MATCH (userName) AGAINST (
                :query IN NATURAL LANGUAGE MODE
            ), 1000) AS relevance5
        FROM Event
        NATURAL JOIN User
        -- Plus pertinent que d'utiliser LIKE
        WHERE MATCH (eventTitle) AGAINST (
            :query IN NATURAL LANGUAGE MODE
        )
        OR MATCH (eventDescription) AGAINST (
            :query IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION
        )
        OR MATCH (eventLocation) AGAINST (
            :query IN NATURAL LANGUAGE MODE
        )
        OR MATCH (userPseudo) AGAINST (
            :query IN NATURAL LANGUAGE MODE
        )
        OR MATCH (userName) AGAINST (
            :query IN NATURAL LANGUAGE MODE
        )
        ORDER BY 
            -- Priorité à ceux qui ne sont pas passés
            eventDate > NOW() DESC,
            -- Technique astucieuse pour définir les priorités de la correspondance
            (
                (relevance1 * 2.5) + 
                (relevance2 * 2) + 
                (relevance3 * 2) + 
                (relevance4 * 1.5) + 
                (relevance5 * 1) 
            ) DESC,
            eventDate DESC
        LIMIT :limit
        OFFSET :offset;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':query', $query);
        $state->bindParam(':limit', $limit, PDO::PARAM_INT);
        $state->bindParam(':offset', $offset, PDO::PARAM_INT);

        $state->execute();

        $data = $state->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $event) {
            $user = new User($event);
            $event['author'] = $user;

            $events[] = new Event($event);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $events;
}

function createEvent(
    PDO $db, 
    string $title, 
    string $description, 
    string $date, 
    string | null $location, 
    int | null $places, 
    string | null $picture,
    int $userId
): int
/**
 * Permet de créer un événement
 * 
 * @var PDO $db Handle de connexion à la base de données
 * @var string $title
 * @var string $description
 * @var string $date
 * @var string | null $location
 * @var int | null $places
 * @var string | null $picture
 * @var int $userId
 * 
 * @return int
 */
{
    $eventId = 0;

    try {
        $sql = <<<'SQL'
        INSERT INTO Event (
            eventTitle,
            eventDescription,
            eventDate,
            eventLocation,
            eventPlaces,
            eventPicture,
            userId
        ) VALUES (
            :title,
            :description,
            :date,
            :location,
            :places,
            :picture,
            :user
        );
SQL;

        $state = $db->prepare($sql);

        $state->bindParam('title', $title);
        $state->bindParam('description', $description);
        $state->bindParam('date', $date);
        $state->bindParam('location', $location);
        $state->bindParam('places', $places, PDO::PARAM_INT);
        $state->bindParam('picture', $picture);
        $state->bindParam('user', $userId, PDO::PARAM_INT);

        $state->execute();
        $eventId = $db->lastInsertId();
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $eventId;
}
