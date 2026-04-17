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

    public static function path(string $path): string
    /**
     * Génère l'URL du site avec le chemin voulu
     * 
     * @var string
     * 
     * @return string URL
     */
    {
        $url = null;
        if (isset($_SERVER['HTTP_HOST'])) {
            $url = self::$protocol . '://' . $_SERVER['HTTP_HOST'] . $path;
        }

        return $url;
    }
}
