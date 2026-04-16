<?php

class Entity {
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
        $value = '';
        if (isset($this->$key) && !empty($this->$key)) {
            $value = (string)$this->$key;
        }

        return htmlspecialchars($value);
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
        if (isset($this->$key)) {
            $value = $this->$key;
        }

        return $value;
    }
}
