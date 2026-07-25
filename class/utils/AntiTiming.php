<?php

class AntiTiming
/**
 * Protection contre les attaques temporelles
 * 
 * (Permet de ne donner aucune indication 
 * sur le temps d'exécution réel d'une 
 * action sensible)
 */
{
    private float | null $waitTime = null;
    private float | null $startTime = null;

    public function __construct(float $waitTime)
    /**
     * @var float $waitTime Temps en milliseconde à attendre
     */
    {
        $this->waitTime = $waitTime;
    }

    public function begin(): void
    /**
     * Permet de démarrer la protection
     * (À placer avant l'action sensible)
     * 
     * @return void
     */
    {
        $this->startTime = microtime(true);
    }

    public function end(): void
    /**
     * Indique la fin de la protection
     * (À placer après l'action sensible)
     * 
     * @return void
     */
    {
        if (empty($this->startTime)) {
            throw new Exception('la méthode begin doit être appellée avant end');
        }

        $elapsedTime = microtime(true) - $this->startTime;

        if ($elapsedTime < $this->waitTime) {
            $microSleep = (int)(
                ($this->waitTime - $elapsedTime) * 1_000_000
            );
            
            usleep($microSleep);
        }
    }
}