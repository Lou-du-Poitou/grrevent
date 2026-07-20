<?php

class HostPath
/**
 * Permet de générer des chemins relatifs du site
 */
{
    public static function path(string ...$args): string
    /**
     * Renvoie un chemin relatif en fonction 
     * des paramètres en entrée
     * 
     * @var string ...$args
     * 
     * @return string PATH
     */
    {
        $path = implode('', $args);

        return $path;
    }

    public static function toUser(string $pseudo): string
    /**
     * Génère le chemin vers le profil d'un utilisateur
     * 
     * @var string $pseudo
     * 
     * @return string PATH
     */
    {
        $filePath = '/user.php';
        $query = '?' . http_build_query([
            'pseudo' => $pseudo
        ]);

        $path = self::path(
            $filePath,
            $query
        );

        return $path;
    }

    public static function toEvent(int $id): string
    /**
     * Génère le chemin vers le profil d'un événement
     * 
     * @var int $id
     * 
     * @return string PATH
     */
    {
        $filePath = '/event.php';
        $query = '?' . http_build_query([
            'id' => $id
        ]);

        $path = self::path(
            $filePath,
            $query
        );

        return $path;
    }

    public static function toSearch(string $q, string $type): string
    /**
     * Génère le chemin vers une recherche
     * 
     * @var string $q Contenu de la recherche
     * @var string $type event|user
     * 
     * @return string PATH
     */
    {
        $filePath = '/recherche.php';
        $query = '?' . http_build_query([
            'q' => $q,
            'type' => $type
        ]);

        $path = self::path(
            $filePath,
            $query
        );

        return $path;
    }

    public static function toPasswordReset(?string $token=null, ?int $id=null): string
    /**
     * Génère le chemin vers la page mot de passe oublié
     * 
     * @var ?string $token=null
     * @var ?int $id=null
     * 
     * @return string PATH
     */
    {
        $filePath = '/motpasse.php';
        $query = '';

        if (!empty($token) && !empty($id)) {
            $query = '?' . http_build_query([
                'token' => $token,
                'id' => $id
            ]);
        }

        $path = self::path(
            $filePath,
            $query
        );

        return $path;
    }

    public static function offsetQuery(string $referer, ?int $offset=null): string
    /**
     * Renvoie le chemin avec le paramètre GET offset
     * 
     * @var string $referer
     * @var ?int $offset=null
     * 
     * @return string PATH
     */
    {
        $parseUrl = parse_url($referer);
        $query = '';
        
        if ($offset) {
            $query = http_build_query([
                'offset' => $offset
            ]);

            if (isset($parseUrl['query'])) {
                $query = '&' . $query;
            } else {
                $query = '?' . $query;
            }
        }

        $path = self::path(
            $referer,
            $query
        );

        return $path;
    }
}
