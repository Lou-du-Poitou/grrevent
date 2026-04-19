<?php
require_once './class/Event.php';
require_once './class/User.php';
require_once './class/HostUrl.php';

require_once './elements/icon.php';

function eventCard(Event $event): string
/**
 * Renvoie les informations d'un événement sous forme de carte
 * 
 * @var Event $event
 * 
 * @return string (Composant HTML)
 */
{
    // Variables d'affichage
    $eventId = $event->getHTML('eventId');
    $eventTitle = $event->getHTML('eventTitle');
    $eventDescription = $event->getHTML('eventDescription');
    $eventPicture = DEFAULT_EVENT_PICTURE;
    if (!empty($event->getValue('eventPicture'))) {
        $eventPicture = $event->getHTML('eventPicture');
    }
    $eventDate = date_format(
        date_create($event->getValue('eventDate')),
        'd/m/Y'
    );
    $eventLocation = $event->getHTML('eventLocation');
    $eventPlaces = $event->getHTML('eventPlaces');

    $eventUrl = HostUrl::pathToEvent($event->getValue('eventId'));

    // Les icones
    $dateIcon = icon('calendar');
    $locationIcon = icon('location-dot');
    $placesIcon = icon('users');

    $html = <<<HTML
    <div class="card">
        <div class="card-title">
            <p><a href="$eventUrl">$eventTitle</a></p>
        </div>
            
        <img src="$eventPicture"
            alt="Photo de l'événement $eventId"
            class="event-pic"
        >

        <div class="card-infos">
            <p>$eventDescription</p>

            <ul>
                <li>$dateIcon $eventDate</li>
                <li>$locationIcon $eventLocation</li>
                <li>$placesIcon $eventPlaces</li>
            </ul>
        </div>
    </div>
HTML;

    return $html;
}

function userCard(User $user): string
/**
 * Renvoie les informations d'un utilisateur sous forme de carte
 * 
 * @var User $user
 * 
 * @return string (Composant HTML)
 */
{
    // Variables d'affichage
    $userPseudo = $user->getHTML('userPseudo');
    $userName = $user->getValue('userName') ? 
        $user->getHTML('userName') : 
        $user->getHTML('userPseudo');
    $userBio = $user->getHTML('userBiography');
    $userPicture = DEFAULT_USER_PICTURE;
    if (!empty($user->getValue('userPicture'))) {
        $userPicture = $user->getHTML('userPicture');
    }
    $userLocation = $user->getHTML('userLocation');
    $userJoinedAt = date_format(
        date_create($user->getValue('userJoinedAt')),
        'd/m/Y'
    );

    $userUrl = HostUrl::pathToUser($user->getValue('userPseudo'));

    // Les icones
    $pseudoIcon = icon('user-tie');
    $locationIcon = icon('location-dot');
    $joinedIcon = icon('calendar-plus');

    $html = <<<HTML
    <div class="card">
        <div class="card-title">
            <p><a href="$userUrl">$userName</a></p>
        </div>
            
        <img src="$userPicture"
            alt="Photo de $userPseudo"
            class="user-pic"
        >

        <div class="card-infos">
            <p>$userBio</p>

            <ul>
                <li>$pseudoIcon $userPseudo</li>
                <li>$locationIcon $userLocation</li>
                <li>$joinedIcon $userJoinedAt</li>
            </ul>
        </div>
    </div>
HTML;

    return $html;
}

function cardsThread(array $entitys, string $referer, int $offset, bool $scroll=true): string 
/**
 * Renvoie un fil d'entités sur lequel on peut défiler
 * 
 * @var array $entitys
 * @var string $referer
 * @var int $offset
 * @var bool $scroll=true
 * 
 * @return string (Composant HTML)
 */
{
    if (!empty($entitys)) {
        // Ajout des événements sous forme de cartes

        $cardsHtml = '';
        foreach ($entitys as $entity) {
            switch (get_class($entity)) {
                case 'Event':
                    $cardsHtml .= eventCard($entity);
                    break;
                case 'User':
                    $cardsHtml .= userCard($entity);
                    break;
                default:
                    // Entité qui ne s'affiche pas sous forme de carte
                    throw new Exception('class non prise en charge');
            }
        }
    } elseif ($offset > 0) {
        // S'il n'y a rien plus rien à afficher

        $cardsHtml = <<<HTML
        <div class="no-content">
            <h1>Plus de contenu</h1>
            <p>Revenez en arrière ou reformuler votre requête</p>
        </div>
HTML;
    } else {
        // Si le résultat est vide dès le départ

        $cardsHtml = <<<HTML
        <div class="no-content">
            <h1>Pas de contenu</h1>
            <p>Cette requête ne renvoie aucun contenu</p>
        </div>
HTML;;
    }
    

    // Pour naviguer dans les 'cards' seulement si $scroll=true
    $previousHtml = '';
    $nextHtml = '';

    if ($scroll) {
        // Précédent
        $previousOffset = $offset - DEFAULT_SELECT_LIMIT;
        $previousLink = $referer . HostUrl::offsetQuery($previousOffset);
        $previousClass = '';
        if ($previousOffset < 0) {
            $previousClass .= 'disabled';
        }

        $previousHtml = <<<HTML
        <a href="$previousLink" class="$previousClass">Précédent</a>
HTML;

        // Suivant
        $nextOffset = $offset + DEFAULT_SELECT_LIMIT;
        $nextLink = $referer . HostUrl::offsetQuery($nextOffset);
        $nextClass = '';
        if (empty($entity)) {
            $nextClass .= 'disabled';
        }

        $nextHtml = <<<HTML
        <a href="$nextLink" class="$nextClass">Suivant</a>
HTML;
    }

    $html = <<<HTML
    <div class="cards-container">
        $cardsHtml

        <div class="cards-scroll">
            $previousHtml

            $nextHtml
        </div>
    </div>
HTML;

    return $html;
}
