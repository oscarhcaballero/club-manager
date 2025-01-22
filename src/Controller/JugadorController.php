<?php

namespace App\Controller;

use App\Entity\Jugador;
use App\Entity\Entrenador;
use App\Entity\Club;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

 
class JugadorController extends AbstractController
{
    #[Route('/api/jugador', methods: ['POST'])]
    public function createJugador(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nombre = $data['nombre'] ?? null;
         

        if (!$nombre) {
            return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
        }

        $jugador = new Jugador();
        $jugador->setNombre($nombre);


        $em->persist($jugador);
        $em->flush();

        return new JsonResponse(['message' => '¡Jugador creado!'], 201);
    }


    #[Route('/api/club/{id}/jugador', methods: ['POST'])]
    public function addJugadorToClub(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $club = $em->getRepository(Club::class)->find($id);
        if (!$club) {
            return new JsonResponse(['error' => 'Club no encontrado'], 404);
        }


        $data = json_decode($request->getContent(), true);
        $id_jugador = $data['id_jugador'] ?? null;
        $salario = $data['salario'] ?? null;


        if (!$id_jugador || !$salario) {
            return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
        }

        if (!is_numeric($id_jugador) || !is_numeric($salario)) {
            return new JsonResponse(['error' => 'Datos inválidos'], 400);
        }

        if ($salario < 0) {
            return new JsonResponse(['error' => 'El salario debe ser positivo.'], 400);
        }

        // el jugador debe existir previamente
        $jugador = $em->getRepository(Jugador::class)->find($id_jugador);
        if (!$jugador) {
            return new JsonResponse(['error' => 'Jugador no encontrado'], 404);
        }

        
        // el jugador no puede estar ya en un club
        if ($jugador->getClub()) {
            if ($id == $jugador->getClub()->getId()) {
                return new JsonResponse(['error' => 'El jugador ya pertenece a este Club'], 400);
            } else {
                return new JsonResponse(['error' => 'El jugador ya pertenece a otro Club'], 400);
            }
        }


        // Comprobamos que el salario no sobrepase el presupuesto del Club
        $salarioTotalActual = 0;
        foreach ($club->getJugadores() as $player) {
            $salarioTotalActual += $player->getSalario();
        }
        
        foreach ($club->getEntrenadores() as $coach) {
            $salarioTotalActual += $coach->getSalario();
        }

        if ($salarioTotalActual + $salario > $club->getPresupuesto()) {
            return new JsonResponse(['error' => 'El presupuesto del Club no permite pagar ese salario.'], 400);
        }


        $jugador->setSalario((float)$salario);
        $jugador->setClub($club);

        $em->persist($jugador);
        $em->flush();

        return new JsonResponse(['message' => '¡Jugador agregado al Club!'], 201);
    }



    

    #[Route('/api/jugador/{id}/baja', methods: ['DELETE'])]
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

            return new JsonResponse(['message' => 'Jugador dado de baja del club'], 200);
        } else {
            return new JsonResponse(['error' => 'El Jugador no pertenece a ningún Club'], 400);
        }

        
    }




}
