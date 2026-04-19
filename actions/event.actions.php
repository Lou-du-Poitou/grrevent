<?php
require_once './config/connection.php';

require_once './class/User.php';
require_once './class/Event.php';

require_once './elements/error.exit.php';

function selectEvent(PDO $db, int $id): Event | false
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

function isAddedEvent(PDO $db, int $loggedId, int $eventId): bool
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

function addEvent(PDO $db, int $loggedId, int $eventId): true
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

function removeEvent(PDO $db, int $loggedId, int $eventId): true
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

function deleteEvent(PDO $db, int $eventId): true
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

function selectUserEvents(PDO $db, int $userId, int $limit, int $offset): array
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
        LIMIT :limit
        OFFSET :offset;
SQL;

        $state = $db->prepare($sql);

        $state->bindParam(':user', $userId, PDO::PARAM_INT);
        $state->bindParam(':limit', $limit, PDO::PARAM_INT);
        $state->bindParam(':offset', $offset, PDO::PARAM_INT);

        $state->execute();
        $data = $state->fetchAll();

        foreach ($data as $event) {
            $events[] = new Event($event);
        }
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $events;
}
