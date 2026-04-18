<?php

class Entity
/**
 * Entité de l'application (ex: Utilisateur)
 */
{
    public function __construct(array $data=[])
    /**
     * Constructeur, remplit automatiquement les propriétés
     * 
     * @var array $data
     */
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getValue(string $key): mixed 
    /**
     * Renvoie la valeur associé une clé d'une entité
     * (ex: $user->getValue('userPseudo');)
     * 
     * @var string $key 
     * 
     * @return mixed
     */
    {
        $value = null;
        if (property_exists($this, $key)) {
            $value = $this->$key;
        } else {
            throw new Exception('propriété invalide');
        }

        return $value;
    }

    public function getHTML(string $key): string
    /**
     * Renvoie la valeur associé une clé d'une entité puis encode les entitées HTML
     * (ex: $user->getHTML('userPseudo');)
     * 
     * @var string $key 
     * 
     * @return string
     */
    {
        $value = (string)$this->getValue($key);

        return htmlspecialchars($value);
    }
}
