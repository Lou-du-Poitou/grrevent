<?php
require_once __DIR__ . '/../../config/constants.php';

require_once __DIR__ . '/../../class/utils/Captcha.php';

$captcha = new Captcha();
$captcha->new(); // Régénération d'une clé
$captchaKey = $captcha->get();

/**
 * Génération de l'image CAPTCHA envoyée
 * au client
 */

header("Content-Type: image/png");

// Création de l'image
$width = 200; $height = 50;
$captchaImage = imagecreate($width, $height);
$backgroundImage = imagecolorallocate(
    $captchaImage,
    127, 127, 127
);

// Ajout des lettres sur l'image
$black = imagecolorallocate(
    $captchaImage,
    255, 255, 255
);
$white = imagecolorallocate(
    $captchaImage,
    0, 0, 0
);

$textColors = [
    $black,
    $white
];

$textFont = __DIR__ . '/arial.ttf';

$initialPos = 20;
$letterSpacing = (int)($width / (CAPTCHA_LENGTH + 1));
for ($i = 0; $i < CAPTCHA_LENGTH; $i++) {
    imagettftext(
        $captchaImage,
        rand(15, 20),
        rand(-20, 20),
        $initialPos + $i*$letterSpacing,
        rand(20, 40), 
        $textColors[rand(0, 1)],
        $textFont,
        $captchaKey[$i]
    );
}

// 10 lignes aléatoires pour brouiller
for ($i = 0; $i < 10; $i++) {
    $rgb = rand(100, 180);
    $lineColor = imagecolorallocate(
        $captchaImage,
        $rgb, $rgb, $rgb
    );

    imageline(
        $captchaImage,
        rand(0, $width),
        rand(0, $height),
        rand(0, $width),
        rand(0, $height),
        $lineColor
    );
}

// 100 pixels aléatoires pour brouiller
for ($i = 0; $i < 100; $i++) {
    $rgb = rand(100, 180);
    $pixelColor = imagecolorallocate(
        $captchaImage,
        $rgb, $rgb, $rgb
    );

    imagesetpixel(
        $captchaImage,
        rand(0, $width),
        rand(0, $height),
        $pixelColor
    );
}

imagepng($captchaImage);
imagedestroy($captchaImage);
