<?php
namespace App\Controller;

use App\Service\NotificadorInterface;
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
        $this->notificador->notificar(
            'Este es un mensaje de prueba',
            'destinatario@ejemplo.com'
        );

        return new JsonResponse(['message' => 'Notificación enviada']);
    }
}
