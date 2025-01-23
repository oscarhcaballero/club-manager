<?php

namespace App\Service\Notificador;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Clase NotificadorEmail 
 * (Estrategia concreta en el patrón Strategy) 
 * (Observador concreto en el patrón Observer)
 *
 * Esta clase implementa la interfaz NotificadorInterface y se encarga de enviar notificaciones por correo electrónico.
 * Utiliza el componente Mailer de Symfony para enviar los correos electrónicos.
 *
 */
class NotificadorEmail implements NotificadorInterface
{
    private $mailer;
    private $fromEmail;

    /**
     * Constructor
     *
     * @param MailerInterface $mailer El servicio de correo de Symfony.
     * @param string $fromEmail La dirección de correo electrónico del remitente.
     */
    public function __construct(MailerInterface $mailer, string $fromEmail)
    {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
    }

    /**
     * Envía una notificación por correo electrónico.
     *
     * @param string $mensaje El contenido del mensaje a enviar.
     * @param object $destinatario El objeto destinatario que contiene la dirección de correo electrónico.
     */
    public function notificar(string $mensaje, object $destinatario): void
    {
        if (!method_exists($destinatario, 'getEmail')) {
            throw new \InvalidArgumentException('El objeto destinatario debe tener un método getEmail.');
        }

        // Comprobamos que el destinatario tenga email
        $emailAddress = $destinatario->getEmail();
        if (is_null($emailAddress) || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('El destinatario debe tener un email válido.');
        }


        $email = (new Email())
            ->from($this->fromEmail)
            ->to($emailAddress)
            ->subject('Notificación del Club')
            ->text($mensaje);

        $this->mailer->send($email);
    }
}
