<?php
require './class/Entity.php';
require './class/User.php';

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

    // Utilisateur auteur de l'événement
    protected User $author;
}
