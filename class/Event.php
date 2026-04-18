<?php
require_once './class/Entity.php';
require_once './class/User.php';

class Event extends Entity
/**
 * Événement publié sur le site
 */
{
    protected int $eventId;
    protected string $eventTitle;
    protected string $eventDescription;
    protected string $eventDate;
    protected int | null $eventPlaces;
    protected string $eventLocation;
    protected string | null $eventPicture;

    // Utilisateur auteur de l'événement
    protected User $author;
}
