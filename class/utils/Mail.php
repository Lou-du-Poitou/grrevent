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
    private array $headers = [];
    private string $message = '';

    public function __construct(string $to, bool $html=false)
    {
        $this->to = $to;

        if (FROM_EMAIL_NAME && FROM_EMAIL) {
            $this->addHeader('From', FROM_EMAIL_NAME . ' <' . FROM_EMAIL . '>');
        }

        if ($html) {
            $this->addHeader('Content-type', 'text/html;charset=UTF-8');
        }
    }

    public function addHeader(string $header, string $value): void
    /**
     * Permet d'ajouter un en-tête au mail
     * 
     * @var string $header
     * @var string $value
     * 
     * @return void
     */
    {
        $this->headers[$header] = $value;
    }

    public function addHeaders(array $headers): void
    /**
     * Permet d'ajouter plusieurs en-tête au mail
     * 
     * @var array $headers
     * 
     * @return void
     */
    {
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value);
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
