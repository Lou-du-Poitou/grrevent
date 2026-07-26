<?php
require_once __DIR__ . '/../../config/constants.php';

require_once __DIR__ . '/Session.php';

class Captcha
/**
 * Permet de gérer un Captcha
 */
{
    private const KEY = 'captcha_key';

    public function __construct()
    {
        Session::init();
    }

    public function get(): string | null
    {
        return $_SESSION[self::KEY] ?? null;
    }

    public function new(): void
    {
        $randomString = '';
        for ($i = 0; $i < CAPTCHA_LENGTH; $i++) {
            $randomChar = CAPTCHA_CHARS[random_int(0, CAPTCHA_CHARS_LENGTH - 1)];
            $randomString .= $randomChar;
        }

        $_SESSION[self::KEY] = $randomString;
    }
}
