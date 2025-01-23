<?php 
namespace App\Service\Observador;

use App\Service\Notificador\NotificadorInterface;

interface SubjectInterface
{
    public function agregarObservador(NotificadorInterface $notificador): void;
    public function notificar(string $mensaje, object $destinatario): void;
}

