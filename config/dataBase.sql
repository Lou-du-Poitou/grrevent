/*
* ProjetWeb - L1 INF09 (c) 2026
*
* Par convention :
*
* - Le nom des tables en PascalCase
* s'il s'agit d'une association entre
* deux tables, le nom de l'association doit
* apparaître entre le nom des deux tables
*
* - Le nom des colonnes en camelCase
* avec le nom de la table en premier
* sauf pour les clés étrangères
*
* /!\ Les nom en anglais, seuls les commentaire
* peuvent être en français.
*/

-- Pour mettre la base de données sur l'heure de Paris
SET `time_zone` = "+02:00";

/*
* Pour la création des tables merci de respecter ce format :
*
* -- Table contenant ...
* CREATE TABLE IF NOT EXISTS `Name` (
*   -- Attributs en premier
*   `nameId` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
*   `nameAttribute` TYPE ... NOT NULL
*       -- REGEX ...
*       CHECK (`nameAttribute` REGEXP "^.*$")
*       DEFAULT ...,
*
*   -- Clés étrangères en deuxième
*   `foreignId` BIGINT UNSIGNED NOT NULL,
*
*   -- Référencement des clés étrangères en troisième
*   FOREIGN KEY (`foreignId`) REFERENCES `Foreign`(`foreignId`)
*       ON DELETE ...,
*
*   -- Définition des clés et index en dernier
*   PRIMARY KEY (`nameId`),
*   UNIQUE KEY (`...`),
*   FULLTEXT(`...`)
* );
*
*/

-- I: Partie principale de l'application
-- ---------------------------------- --

-- Table contenant les informations d'un utilisateur
CREATE TABLE IF NOT EXISTS `User` (
    `userId` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `userPseudo` VARCHAR(35) NOT NULL
        -- REGEX du pseudo d'un utilisateur
        CHECK (`userPseudo` REGEXP "^[a-z][a-z0-9_]{2,34}$"),
    `userName` VARCHAR(35),
    `userEmail` VARCHAR(100) NOT NULL
        -- REGEX d'un email valide
        CHECK (`userEmail` REGEXP "^[-.+a-zA-Z0-9_]+@[-.+a-zA-Z0-9_]+\.[a-zA-Z0-9_]{2,}$"),
    `userPassword` VARCHAR(60) NOT NULL
        -- REGEX bcrypt hash trouvé sur internet :
        -- https://stackoverflow.com/questions/31417387/regular-expression-to-find-bcrypt-hash
        CHECK (`userPassword` REGEXP "^[$]2[abxy]?[$](?:0[4-9]|[12][0-9]|3[01])[$][./0-9a-zA-Z]{53}$"),
    `userBiography` VARCHAR(500),
    `userPicture` VARCHAR(250)
        -- REGEX url ou chemin d'accès à voire
        CHECK (`userPicture` REGEXP "^http(s)?:\/\/[-.a-z0-9]+\.[a-z]{2,}\/[-.\/a-zA-Z0-9_]+[a-z]{2,}$"),
    /*
    `userBirthday` DATETIME NOT NULL,
    `userVisibility` BIT(2) NOT NULL
        DEFAULT 0,
    */
    `userLocation` VARCHAR(50),
    `userJoinedAt` DATETIME NOT NULL 
        DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`userId`),
    UNIQUE KEY (`userPseudo`),
    UNIQUE KEY (`userEmail`)
);

-- Table contenant les informations d'un événement
CREATE TABLE IF NOT EXISTS `Event` (
    `eventId` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `eventTitle` VARCHAR(120) NOT NULL,
    `eventDescription` VARCHAR(500) NOT NULL,
    `eventDate` DATETIME NOT NULL,
    `eventLocation` VARCHAR(50),
    `eventPlaces` INT UNSIGNED -- On peut passer à BIGINT (à voire)
        DEFAULT NULL,

    `userId` BIGINT UNSIGNED NOT NULL,

    FOREIGN KEY (`userId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,

    PRIMARY KEY (`eventId`),
    FULLTEXT(`eventTitle`),
    FULLTEXT(`eventDescription`)
);

-- Table contenant les informations d'une catégorie
CREATE TABLE IF NOT EXISTS `Category` (
    `categoryId` BIGINT UNSIGNED NOT NULL,
    `categoryName` VARCHAR(50) NOT NULL,

    PRIMARY KEY (`categoryId`)
);

-- Table contenant les événements ajoutés par un utilisateur
CREATE TABLE IF NOT EXISTS `UserAddEvent` (
    `userId` BIGINT UNSIGNED NOT NULL,
    `eventId` BIGINT UNSIGNED NOT NULL,

    FOREIGN KEY (`userId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,
    FOREIGN KEY (`eventId`) REFERENCES `Event`(`eventid`)
        ON DELETE CASCADE,

    PRIMARY KEY (`userId`, `eventId`)
);

-- Table contenant les catégories associées à un événement
CREATE TABLE IF NOT EXISTS `EventHasCategory` (
    `eventId` BIGINT UNSIGNED NOT NULL,
    `categoryId` BIGINT UNSIGNED NOT NULL,

    FOREIGN KEY (`eventId`) REFERENCES `Event`(`eventid`)
        ON DELETE CASCADE,
    FOREIGN KEY (`categoryId`) REFERENCES `Category`(`categoryId`)
        ON DELETE CASCADE,

    PRIMARY KEY (`eventId`, `categoryId`)
);

-- Table contenant les suivis d'un utilisateur
CREATE TABLE IF NOT EXISTS `UserFollowUser` (
    `followerId` BIGINT UNSIGNED NOT NULL,
    `followedId` BIGINT UNSIGNED NOT NULL,

    FOREIGN KEY (`followerId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,
    FOREIGN KEY (`followedId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,

    PRIMARY KEY (`followerId`, `followedId`)
);

-- Table contant les tokens d'un utilisateur (pour reset le mot de passe)
CREATE TABLE IF NOT EXISTS `Token` (
    `userId` BIGINT UNSIGNED NOT NULL,
    `tokenValue` VARCHAR(60) NOT NULL
        CHECK (`tokenValue` REGEXP "^[$]2[abxy]?[$](?:0[4-9]|[12][0-9]|3[01])[$][./0-9a-zA-Z]{53}$"),
    `tokenExpires` DATETIME NOT NULL
        DEFAULT (NOW() + INTERVAL 48 HOUR),

    FOREIGN KEY (`userId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,

    PRIMARY KEY (`userId`)
);

-- II: Partie support de l'application
-- -------------------------------- --
-- /!\ PAS LE TEMPS /!\ --
/*
-- Table contenant les utilisateurs support
CREATE TABLE IF NOT EXISTS `Support` (
    `supportId` BIGINT UNSIGNED NOT NULL,
    `supportJoinedAt` DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,
    `supportLevel` TINYINT NOT NULL -- On verra
        DEFAULT 0,

    FOREIGN KEY (`supportId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,

    PRIMARY KEY (`supportId`)
);

-- Table contanent les requètes envoyées par un utiilisateur
CREATE TABLE IF NOT EXISTS `Request` (
    `requestId` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `requestDate` DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,
    `requestStatus` BIT(2) NOT NULL
        DEFAULT 0,
    `requestSubject` VARCHAR(120) NOT NULL,

    `userId` BIGINT UNSIGNED NOT NULL,
    `supportId` BIGINT UNSIGNED
        DEFAULT NULL,

    FOREIGN KEY (`userId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,
    FOREIGN KEY (`supportId`) REFERENCES `Support`(`supportId`)
        ON DELETE SET NULL,

    PRIMARY KEY (`requestId`)
);

-- Table contenant les messages d'une requète
CREATE TABLE IF NOT EXISTS `Message` (
    `messageId` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `messageContent` VARCHAR(500) NOT NULL,

    `userId` BIGINT UNSIGNED NOT NULL,
    `requestId` BIGINT UNSIGNED NOT NULL,
    -- Message auquel il répond
    `parentId` BIGINT UNSIGNED
        DEFAULT NULL,

    FOREIGN KEY (`userId`) REFERENCES `User`(`userId`)
        ON DELETE CASCADE,
    FOREIGN KEY (`requestId`) REFERENCES `Request`(`requestId`)
        ON DELETE CASCADE,
    FOREIGN KEY (`parentId`) REFERENCES `Message`(`messageId`)
        ON DELETE CASCADE,

    PRIMARY KEY (`messageId`)
);
*/
/**
* /!\ Lire la convention en haut de page avant
* de commencer à modifier le fichier
*/
