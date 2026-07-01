<?php
require_once './class/models/Event.php';
require_once './class/models/User.php';
require_once './class/utils/HostUrl.php';

require_once './elements/icon.php';

/**
 * Les deux cartes Utilisateur/Événement sont très proches
 * d'un point de vue de la structure HTML, cette duplication
 * de code est voulu car ces structures peuvent évoluer avec
 * le développement de l'application.
 */

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
        'd/m/Y H\hi'
    );
    $eventLocation = $event->getHTML('eventLocation');
    $eventPlaces = $event->getHTML('eventPlaces');
    $authorPseudo = $event->getValue('author')->getHTML('userPseudo');

    $eventUrl = htmlspecialchars(
        HostUrl::pathToEvent($event->getValue('eventId'))
    );

    $authorUrl = htmlspecialchars(
        HostUrl::pathToUser($event->getValue('author')->getValue('userPseudo'))
    );

    // Génération des infos en dynamique
    $authorIcon = icon('feather-pointed');
    $authorHTML = <<<HTML
    <li>$authorIcon <a href="$authorUrl">$authorPseudo</a></li>
HTML;

    $dateIcon = icon('calendar');
    $dateHTML = <<<HTML
    <li>$dateIcon $eventDate</li>
HTML;

    $locationHTML = null;
    if (!empty($eventLocation)) {
        $locationIcon = icon('location-dot');
        $locationHTML = <<<HTML
        <li>$locationIcon $eventLocation</li>
HTML;
    }

    $placesHTML = null;
    if (!empty($eventPlaces)) {
        $placesIcon = icon('users');
        $placesHTML = <<<HTML
        <li>$placesIcon $eventPlaces</li>
HTML;
    }

    $html = <<<HTML
    <div class="card">
        <div class="card-title">
            <p><a href="$eventUrl">$eventTitle</a></p>
        </div>
            
        <div class="card-image">
            <img src="$eventPicture"
                alt="Photo de l'événement $eventId"
                class="event-pic"
            >
        </div>

        <div class="card-infos">
            <p>$eventDescription</p>

            <ul>
                $authorHTML
                $dateHTML
                $locationHTML
                $placesHTML
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

    $userUrl = htmlspecialchars(
        HostUrl::pathToUser($user->getValue('userPseudo'))
    );

    // Génération des infos en dynamique

    $pseudoIcon = icon('user-tie');
    $pseudoHTML = <<<HTML
    <li>$pseudoIcon $userPseudo</li>
HTML;

    $locationHTML = null;
    if (!empty($userLocation)) {
        $locationIcon = icon('location-dot');
        $locationHTML = <<<HTML
        <li>$locationIcon $userLocation</li>
HTML;
    }

    $html = <<<HTML
    <div class="card">
        <div class="card-title">
            <p><a href="$userUrl">$userName</a></p>
        </div>
            
        <div class="card-image">
            <img src="$userPicture"
                alt="Photo de $userPseudo"
                class="user-pic"
            >
        </div>

        <div class="card-infos">
            <p>$userBio</p>

            <ul>
                $pseudoHTML
                $locationHTML
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
HTML;
    }
    

    // Pour naviguer dans les 'cards' seulement si $scroll=true
    $previousHtml = '';
    $nextHtml = '';

    if ($scroll) {
        // Précédent
        $previousOffset = $offset - DEFAULT_SELECT_LIMIT;
        $previousLink = htmlspecialchars(
            HostUrl::offsetQuery($referer, $previousOffset)
        );
        $previousClass = '';
        if ($previousOffset < 0) {
            $previousClass .= 'disabled';
        }

        $previousHtml = <<<HTML
        <a href="$previousLink" class="$previousClass">Précédent</a>
HTML;

        // Suivant
        $nextOffset = $offset + DEFAULT_SELECT_LIMIT;
        $nextLink = htmlspecialchars(
            HostUrl::offsetQuery($referer, $nextOffset)
        );
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
