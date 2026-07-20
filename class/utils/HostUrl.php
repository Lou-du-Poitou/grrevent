<?php
require_once __DIR__ . '/HostPath.php';

class HostUrl
/**
 * Permet de gérer les URL complètes du site
 */
{
    private static string $protocol = 'http';

    public function __construct()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $this->protocol .= 's';
        }
    }

    private static function url(string $path): string | null
    /**
     * Renvoie l'URL en fonction des paramètres en entrée
     * 
     * @var string $path
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

    public static function __callStatic(string $name, array $arguments)
    {
        if (method_exists(HostPath::class, $name)) {
            return self::url(
                HostPath::$name(...$arguments)
            );
        }

        throw new Exception('méthode inconnue');
    }
}
