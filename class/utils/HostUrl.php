<?php

class HostUrl
/**
 * Permet de gérer les URL du site
 */
{
    private static string $protocol = 'http';

    public function __construct()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $this->protocol .= 's';
        }
    }

    private static function url(string ...$path): string | null
    /**
     * Renvoie l'url en fonction des paramètres en entrée
     * 
     * @var string ...$path
     * 
     * @return string URL
     */
    {
        $url = null;
        if (isset($_SERVER['HTTP_HOST'])) {
            $url = self::$protocol . '://' . $_SERVER['HTTP_HOST'] . implode('', $path);
        }

        return $url;
    }

    public static function path(string $path): string
    /**
     * Génère l'URL du site avec le chemin voulu
     * 
     * @var string $path
     * 
     * @return string URL
     */
    {
        $url = self::url($path);

        return $url;
    }

    public static function pathToUser(string $pseudo): string
    /**
     * Génère l'URL vers le profil d'un utilisateur
     * 
     * @var string $pseudo
     * 
     * @return string URL
     */
    {
        $filePath = '/user.php';
        $query = '?pseudo=' . $pseudo;

        $url = self::url(
            $filePath,
            $query
        );

        return $url;
    }

    public static function pathToEvent(int $id): string
    /**
     * Génère l'URL vers le profil d'un événement
     * 
     * @var int $id
     * 
     * @return string URL
     */
    {
        $filePath = '/event.php';
        $query = '?id=' . $id;

        $url = self::url(
            $filePath,
            $query
        );

        return $url;
    }

    public static function pathToSearch(string $q, string $type): string
    /**
     * Génère l'URL vers une recherche
     * 
     * @var string $q Contenu de la recherche
     * @var string $type event|user
     * 
     * @return string URL
     */
    {
        $filePath = '/recherche.php';
        $query = '?q=' . $q . '&type=' . $type;

        $url = self::url(
            $filePath,
            $query
        );

        return $url;
    }

    public static function pathPasswordReset(?string $token=null, ?int $id=null): string
    /**
     * Génère l'URL vers la page mot de passe oublié
     * 
     * @var ?string $token=null
     * @var ?int $id=null
     * 
     * @return string URL
     */
    {
        $filePath = '/motpasse.php';
        $queryToken = null;
        $queryId = null;

        if (!empty($token) && !empty($id)) {
            $queryToken = '?token=' . $token;
            $queryId = '&id=' . $id;
        }

        $url = self::url(
            $filePath,
            $queryToken,
            $queryId
        );

        return $url;
    }

    public static function offsetQuery(string $referer, ?int $offset=null): string
    /**
     * Renvoie le paramètre GET offset
     * 
     * @var string $referer
     * @var ?int $offset=null
     * 
     * @return string PARAM GET URL
     */
    {
        $url = $referer;
        $parseUrl = parse_url($referer);
        
        if ($offset) {
            if (isset($parseUrl['query'])) {
                $url .= '&offset=' . $offset;
            } else {
                $url .= '?offset=' . $offset;
            }
        }

        return $url;
    }
}
