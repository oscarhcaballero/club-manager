<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificador implements NotificadorInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notificar(string $mensaje, string $destinatario): void
    {
        $email = (new Email())
            ->from('noreply@clubmanager.com')
            ->to($destinatario)
            ->subject('Notificación del Club')
            ->text($mensaje);

        $this->mailer->send($email);
    }
}
