<?php

namespace App\Controller;

use App\Entity\Jugador;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Observador\SubjectInterface;
use App\Service\Notificador\NotificadorEmail;
 

class JugadorController extends AbstractController
{
    private $subject; 

    /**
     * Constructor
     *
     * @param SubjectInterface $subject El sujeto que maneja la lista de observadores
     * @param NotificadorEmail $notificadorEmail El observador concreto que se agregará a la lista de observadores que maneja el sujeto
     */
    public function __construct(SubjectInterface $subject, NotificadorEmail $notificadorEmail)
    {
        $this->subject = $subject;
        $this->subject->agregarObservador($notificadorEmail);
        //$this->subject->agregarObservador($notificadorSMS);
        //$this->subject->agregarObservador($notificadorWhatsapp);
    }


    #[Route('/api/create-jugador', methods: ['POST'])]
    public function createJugador(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nombre = $data['nombre'] ?? null;
        $email = $data['email'] ?? null;
         

        if (!$nombre) {
            return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
        }

        $jugador = new Jugador();
        $jugador->setNombre($nombre);
        $jugador->setEmail($email);


        $em->persist($jugador);
        $em->flush();

        return new JsonResponse(['message' => '¡Jugador creado!'], 201);
    }


    
    #[Route('/api/jugador/{id}/delete', methods: ['DELETE'])]
    public function removeJugadorFromClub(int $id, EntityManagerInterface $em): JsonResponse
    {
        $jugador = $em->getRepository(Jugador::class)->find($id);
        if (!$jugador) {
            return new JsonResponse(['error' => 'Jugador no encontrado'], 404);
        }

        // recuperamos el club al que pertenece el jugador
        $club = $jugador->getClub();
        if ($club) {

            $club->getJugadores()->removeElement($jugador);
            $jugador->setClub(null);
            $jugador->setSalario(null);

            $em->flush();

            // notificación
            $mensaje = 'Jugador dado de baja del club';
            try {
                $this->subject->notificar($mensaje, $jugador);
                return new JsonResponse([
                    'message' => $mensaje. ', y notificación enviada.'
                ], 200);
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse([
                    'message' => $mensaje. ', pero hubo un problema al enviar la notificación.',
                    'error' => $e->getMessage()
                ], 201);
            }
            
        } else {
            return new JsonResponse(['error' => 'El Jugador no pertenece a ningún Club'], 400);
        }

        
    }




}
