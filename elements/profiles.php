<?php
require_once './elements/inputs.php';

require_once './actions/user.actions.php';
require_once './actions/event.actions.php';

require_once './class/models/User.php';
require_once './class/models/Event.php';
require_once './class/utils/Logged.php';
require_once './class/utils/CSRFToken.php';

function backButton(): string
/**
 * Renvoie le bouton pour revenir à la page précédente
 * 
 * @return string (Composant HTML)
 */
{
    $back = htmlspecialchars(
        $_SERVER['HTTP_REFERER'] ?? 'index.php'
    );

    $html = <<<HTML
    <div class="back-btn">
        <a href="$back">
            <button>Retour</button>
        </a>
    </div>
HTML;

    return $html;
}

function profileHeader(
    string $keyword, 
    string $value, 
    ?string $link=null
): string
/**
 * Renvoie un élément header d'un profil
 * 
 * @var string $keyword
 * @var string $value
 * @var ?string $link=null
 * 
 * @return string (Composant HTML)
 */
{
    $set = htmlspecialchars($value);
    $keyword = htmlspecialchars($keyword);
    if ($link) {
        $link = htmlspecialchars($link);
        $set = <<<HTML
        <a href="$link">$set</a>
HTML;
    }

    $html = <<<HTML
    <p>
        <b>$keyword:</b> $set
    </p>
HTML;

    return $html;
}

function followUserHandler(
    User $user, 
    string $referer, 
    string $requestUri, 
    bool $isFollow=false
): string
/**
 * Renvoie le bouton permettant de suivre/ne plus suivre un utilisateur
 * 
 * @var User $user
 * @var string $referer
 * @var bool $isFollow=false
 * 
 * @return string (Composant HTML)
 */
{
    $csrfToken = new CSRFToken();

    $userId = (int)$user->getValue('userId');
    $requestUri = htmlspecialchars($requestUri);

    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $referer)) {
        if (
            isset($_POST['csrf_token']) && 
            isset($_POST['follow']) && count($_POST) === 2
        ) {
            // Vérification du token CSRF
            $csrfCheck = hash_equals(
                $csrfToken->get(), 
                $_POST['csrf_token']
            );

            if ($csrfCheck) {
                $logged = new Logged();
                $loggedId = $logged->user()->getValue('userId');
        
                $db = connection();
        
                if (!$isFollow) {
                    followUser($db, $loggedId, $userId);
                } else {
                    unfollowUser($db, $loggedId, $userId);
                }
        
                // On met à jour le status de suivi
                $isFollow = !$isFollow;
                
                $db = null;
            }
        }
    }

    if (!$isFollow) {
        $titleButton = 'Suivre';
        $iconButton = 'user-plus';
    } else {
        $titleButton = 'Suivi·e';
        $iconButton = 'user-minus';
    }

    $button = buttonInput($titleButton, 
        'submit', 
        false, 
        $iconButton
    );

    $hiddenFollow = hiddenInput(
        "follow", 
        $userId
    );

    $hiddenCSRF = hiddenInput(
        "csrf_token",
        $csrfToken->get()
    );

    $html = <<<HTML
    <form action="$requestUri" method="post">
        $hiddenFollow
        $hiddenCSRF
        $button
    </form>
HTML;

    return $html;
}

function addEventHandler(
    Event $event, 
    string $referer, 
    string $requestUri, 
    bool $isAdded=false
): string
/**
 * Renvoie le bouton permettant d'ajouter/retirer un événement
 * 
 * @var Event $event
 * @var string $referer
 * @var bool $isAdded=false
 * 
 * @return string (Composant HTML)
 */
{
    $csrfToken = new CSRFToken();

    $eventId = (int)$event->getValue('eventId');
    $requestUri = htmlspecialchars($requestUri);

    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $referer)) {  
        if (
            isset($_POST['csrf_token']) && 
            isset($_POST['add']) && count($_POST) === 2
        ) {
            // Vérification du token CSRF
            $csrfCheck = hash_equals(
                $csrfToken->get(), 
                $_POST['csrf_token']
            );

            if ($csrfCheck) {
                $logged = new Logged();
                $loggedId = $logged->user()->getValue('userId');
        
                $db = connection();
        
                if (!$isAdded) {
                    addEvent($db, $loggedId, $eventId);
                } else {
                    removeEvent($db, $loggedId, $eventId);
                }
        
                // On met à jour le status d'ajout
                $isAdded = !$isAdded;
                
                $db = null;
            }
        }
    }

    if (!$isAdded) {
        $titleButton = 'Ajouter';
        $iconButton = 'plus';
    } else {
        $titleButton = 'Retirer';
        $iconButton = 'minus';
    }

    $button = buttonInput($titleButton, 
        'submit', 
        false, 
        $iconButton
    );

    $hiddenAdd = hiddenInput(
        "add",
        $eventId
    );

    $hiddenCSRF = hiddenInput(
        "csrf_token",
        $csrfToken->get()
    );

    $html = <<<HTML
    <form action="$requestUri" method="post">
        $hiddenAdd
        $hiddenCSRF
        $button
    </form>
HTML;

    return $html;
}

function deleteEventHandler(
    Event $event, 
    string $referer, 
    string $requestUri
): string
/**
 * Permet de supprimer un événement
 * 
 * @var Event $event
 * @var string $referer
 * @var bool $isAdded=false
 * 
 * @return string (Composant HTML)
 */
{
    $csrfToken = new CSRFToken();

    $eventId = (int)$event->getValue('eventId');
    $authorId = (int)$event->getValue('author')->getValue('userId');
    $requestUri = htmlspecialchars($requestUri);

    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $referer)) {
        if (
            isset($_POST['csrf_token']) && 
            isset($_POST['delete']) && count($_POST) === 2
        ) {
            // Vérification du token CSRF
            $csrfCheck = hash_equals(
                $csrfToken->get(), 
                $_POST['csrf_token']
            );

            if ($csrfCheck) {
                $logged = new Logged();
                $loggedId = (int)$logged->user()->getValue('userId');
        
                if ($authorId === $loggedId) {
                    $db = connection();
                    
                    deleteEvent($db, $eventId);
        
                    $db = null;
        
                    header('Location: index.php');
                }
            }
        }
    }

    $titleButton = 'Supprimer';
    $iconButton = 'trash';

    $button = buttonInput($titleButton, 
        'submit', 
        false, 
        $iconButton
    );

    $deleteHidden = hiddenInput(
        "delete",
        $eventId
    );

    $hiddenCSRF = hiddenInput(
        "csrf_token",
        $csrfToken->get()
    );

    $html = <<<HTML
    <form action="$requestUri" method="post">
        $deleteHidden
        $hiddenCSRF
        $button
    </form>
HTML;

    return $html;
}
