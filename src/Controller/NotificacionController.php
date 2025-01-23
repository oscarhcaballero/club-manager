<?php
namespace App\Controller;

use App\Service\Notificador\NotificadorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificacionController extends AbstractController
{
    private $notificador;

    public function __construct(NotificadorInterface $notificador)
    {
        $this->notificador = $notificador;
    }

    #[Route('/test-notificacion', methods: ['GET'])]
    public function testNotificacion(): JsonResponse
    {
        // Crear un objeto destinatario con un método getEmail()
        $destinatario = new class {
            private $email = 'destinatario@ejemplo.com';

            public function getEmail(): string
            {
                return $this->email;
            }
        };

        try {
            $this->notificador->notificar(
                'Este es un mensaje de prueba',
                $destinatario
            );
            return new JsonResponse(['message' => 'Notificación enviada']);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
        
    }
}
