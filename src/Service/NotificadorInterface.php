<?php

namespace App\Service;

interface NotificadorInterface
{
    public function notificar(string $mensaje, string $destinatario): void;
}
