<?php 
namespace App\Service\Observador;

use App\Service\Notificador\NotificadorInterface;
/**
 * Sujeto concreto en el patrón Observer, implementa la interfaz SubjectInterface
 */
class Subject implements SubjectInterface
{
    private array $observadores = [];

    /**
     * Agrega un observador a la lista de observadores.
     *
     * @param NotificadorInterface $notificador El observador que se agregará.
     */
    public function agregarObservador(NotificadorInterface $notificador): void
    {
        $this->observadores[] = $notificador;
    }

    /**
     * Notifica a todos los observadores en la lista de observadores.
     *
     * @param string $mensaje El mensaje a enviar a los observadores.
     * @param object $destinatario El destinatario de la notificación.
     */
    public function notificar(string $mensaje, object $destinatario): void
    {
        foreach ($this->observadores as $observador) {
            $observador->notificar($mensaje, $destinatario);
        }
    }
}
