<?php

namespace App\Controller;

use App\Entity\Entrenador;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Observador\SubjectInterface;
use App\Service\Notificador\NotificadorEmail;


class EntrenadorController extends AbstractController
{
    private $subject; 
    
    /**
     * Constructor
     *
     * @param SubjectInterface $subject El sujeto que maneja la lista de observadores
     * @param NotificadorEmail $notificador El observador concreto que se agregará a la lista de observadores que maneja el sujeto
     */
    public function __construct(SubjectInterface $subject, NotificadorEmail $notificadorEmail)
    {
        $this->subject = $subject;
        $this->subject->agregarObservador($notificadorEmail);
    }


    #[Route('/api/create-entrenador', methods: ['POST'])]
    public function createEntrenador(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nombre = $data['nombre'] ?? null;
        $email = $data['email'] ?? null;


        if (!$nombre) {
            return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
        }

        $entrenador = new Entrenador();
        $entrenador->setNombre($nombre);
        $entrenador->setEmail($email);


        $em->persist($entrenador);
        $em->flush();

        return new JsonResponse(['message' => 'Entrenador creado!'], 201);
    }



    #[Route('/api/entrenador/{id}/delete', methods: ['DELETE'])]
    public function removeEntrenadorFromClub(int $id, EntityManagerInterface $em): JsonResponse
    {
        $entrenador = $em->getRepository(Entrenador::class)->find($id);
        if (!$entrenador) {
            return new JsonResponse(['error' => 'Entrenador no encontrado'], 404);
        }

        // recuperamos el club al que pertenece el Entrenador
        $club = $entrenador->getClub();
        if ($club) {

            $club->getEntrenadores()->removeElement($entrenador);
            $entrenador->setClub(null);
            $entrenador->setSalario(null);        

            $em->flush();

            // notificación
            $mensaje = 'Entrenador dado de baja del club';
            try {
                $this->subject->notificar($mensaje, $entrenador);
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
            return new JsonResponse(['error' => 'El Entrenador no pertenece a ningún Club'], 400);
        }

            
    }


}
