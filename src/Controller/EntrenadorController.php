<?php

namespace App\Controller;

use App\Entity\Entrenador;
use App\Entity\Club;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class EntrenadorController extends AbstractController
{
    #[Route('/api/entrenador', methods: ['POST'])]
    public function createEntrenador(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nombre = $data['nombre'] ?? null;
        

        if (!$nombre) {
            return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
        }

        $entrenador = new Entrenador();
        $entrenador->setNombre($nombre);


        $em->persist($entrenador);
        $em->flush();

        return new JsonResponse(['message' => 'Entrenador creado!'], 201);
    }



  
    #[Route('/api/entrenador/{id}/baja', methods: ['DELETE'])]
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

            return new JsonResponse(['message' => 'Entrenador dado de baja del Club'], 200);
        } else {
            return new JsonResponse(['error' => 'El Entrenador no pertenece a ningún Club'], 400);
        }

            
    }


}
