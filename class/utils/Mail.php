<?php
require_once __DIR__ . '/../../config/constants.php';

class Mail
/**
 * Mail pouvant être envoyé à un utilisateur
 * 
 * @var string $to
 * @var bool $html=false Si le mail doit envoyer du html
 */
{
    private string $to = '';
    private string $subject = '';
    private string $headers = "From: " . FROM_EMAIL_NAME . "\r\n";
    private string $message = '';

    public function __construct(string $to, bool $html=false)
    {
        $this->to = $to;
        if ($html) {
            $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        }
    }

    public function addHeader(string $header): void
    /**
     * Permet d'ajouter un en-tête au mail
     * 
     * @var string header
     * 
     * @return void
     */
    {
        $this->headers .= $header . "\r\n";
    }

    public function addHeaders(array $headers): void
    /**
     * Permet d'ajouter plusieurs en-tête au mail
     * 
     * @var string header
     * 
     * @return void
     */
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }

    public function setSubject(string $subject): void
    /**
     * Permet de définir le sujet du mail
     * 
     * @var string $subject
     * 
     * @return void
     */
    {
        $this->subject = $subject;
    }

    public function setMessage(string $message): void
    /**
     * Permet de définir le message du mail
     * 
     * @var string $message
     * 
     * @return void
     */
    {
        $this->message = $message;
    }

    public function send(): bool
    /**
     * Permet d'envoyer le mail
     * 
     * @return bool
     */
    {
        if (!empty($this->to) && !empty($this->message)) {
            return mail(
                $this->to, 
                $this->subject, 
                $this->message, 
                $this->headers
            );
        }

        throw new Exception('destinataire ou message vide');
    }
}
