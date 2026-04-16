<?php

class Entity {
    public function getHTML(string $key): string | null
    /**
     * Renvoie la valeur associé une clé d'une entité puis encode les entitées HTML
     * (ex: $user->getHTML('userPseudo');)
     * 
     * @var string $key 
     * 
     * @return mixed
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
        $value = '';
        if (isset($this->$key) && !empty($this->$key)) {
            $value = $this->$key;
        }

        return $value;
    }
}
