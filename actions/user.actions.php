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
