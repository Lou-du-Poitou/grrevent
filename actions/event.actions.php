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

function selectUserEvents(PDO $db, int $user, int $offset) {
    try {
        $sql = <<<'SQL'

SQL;
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return ;
}
