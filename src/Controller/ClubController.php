<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Jugador;
use App\Entity\Entrenador;
use App\Repository\ClubRepository;
use App\Repository\JugadorRepository;
use App\Repository\EntrenadorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Observador\SubjectInterface;
use App\Service\Notificador\NotificadorInterface;


class ClubController extends AbstractController
{
    private $subject;

    public function __construct(SubjectInterface $subject, NotificadorInterface $notificador)
    {
        $this->subject = $subject;
        $this->subject->agregarObservador($notificador);
    }

    #[Route('/api/club', methods: ['POST'])]
    public function createClub(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nombre = $data['nombre'] ?? null;
        $presupuesto = $data['presupuesto'] ?? null;

        if (!$nombre || !$presupuesto) {
            return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
        }

        if (!is_string($nombre) || !is_numeric($presupuesto)) {
            return new JsonResponse(['error' => 'Datos inválidos'], 400);
        }

        if ($presupuesto < 0) {
            return new JsonResponse(['error' => 'El presupuesto debe ser positivo.'], 400);
        }

        $club = new Club();
        $club->setNombre($nombre);
        $club->setPresupuesto((float) $presupuesto);

        try {
            $em->persist($club);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error al guardar el club'], 500);
        }

        return new JsonResponse(['message' => '¡Club creado!'], 201);
    }


    #[Route('/api/club/{id}/budget', methods: ['PUT'])]
    public function updateBudget(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $club = $em->getRepository(Club::class)->find($id);
        if (!$club) {
            return new JsonResponse(['error' => 'Club no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $nuevoPresupuesto = $data['presupuesto'] ?? null;

        if ($nuevoPresupuesto === null) {
            return new JsonResponse(['error' => 'Presupuesto no proporcionado'], 400);
        }

        // Verificar si el nuevo presupuesto cubre los salarios
        $totalSalarios = 0;
        foreach ($club->getJugadores() as $jugador) {
            $totalSalarios += $jugador->getSalario();
        }
        foreach ($club->getEntrenadores() as $entrenador) {
            $totalSalarios += $entrenador->getSalario();
        }
        
        if ($nuevoPresupuesto < 0) {
            return new JsonResponse(['error' => 'El presupuesto no puede ser negativo'], 400);
        }

        if ($nuevoPresupuesto < $totalSalarios) {
            return new JsonResponse(['error' => 'El presupuesto no puede ser menor a los salarios actuales'], 400);
        }



        $club->setPresupuesto((float) $nuevoPresupuesto);
        $em->flush();

        return new JsonResponse(['message' => '¡Presupuesto actualizado!'], 200);
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

        // notificación
        $mensaje = 'Jugador agregado al Club';
        try {
            $this->subject->notificar($mensaje, $jugador);
            return new JsonResponse([
                'message' => $mensaje. ', y notificación enviada.'
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $mensaje. ', pero hubo un problema al enviar la notificación.',
                'error' => $e->getMessage()
            ], 201);
        }

    }



    #[Route('/api/club/{id}/entrenador', methods: ['POST'])]
    public function addEntrenadorToClub(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $club = $em->getRepository(Club::class)->find($id);
        if (!$club) {
            return new JsonResponse(['error' => 'Club no encontrado'], 404);
        }


        $data = json_decode($request->getContent(), true);
        $id_entrenador = $data['id_entrenador'] ?? null;
        $salario = $data['salario'] ?? null;


        if (!$id_entrenador || !$salario) {
            return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
        }

        if (!is_numeric($id_entrenador) || !is_numeric($salario)) {
            return new JsonResponse(['error' => 'Datos inválidos'], 400);
        }

        if ($salario < 0) {
            return new JsonResponse(['error' => 'El salario debe ser positivo.'], 400);
        }

        // el entrenador debe existir previamente
        $entrenador = $em->getRepository(Entrenador::class)->find($id_entrenador);
        if (!$entrenador) {
            return new JsonResponse(['error' => 'Entrenador no encontrado'], 404);
        }

        
        // el entrenador no puede estar ya en un club
        if ($entrenador->getClub()) {
            if ($id == $entrenador->getClub()->getId()) {
                return new JsonResponse(['error' => 'El entrenador ya pertenece a este Club'], 400);
            } else {
                return new JsonResponse(['error' => 'El entrenador ya pertenece a otro Club'], 400);
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


        $entrenador->setSalario((float)$salario);
        $entrenador->setClub($club);

        $em->persist($entrenador);
        $em->flush();


        // notificación
        $mensaje = 'Entrenador agregado al Club';
        try {

            $this->subject->notificar($mensaje, $entrenador);
            return new JsonResponse([
                'message' => $mensaje. ', y notificación enviada.'
            ], 200);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $mensaje. ', pero hubo un problema al enviar la notificación.',
                'error' => $e->getMessage()
            ], 201);

        }

    }




    #[Route('/api/club/{clubId}/jugadores', methods: ['GET'])]
    public function listJugadoresFromClub(int $clubId, Request $request, EntityManagerInterface $em, JugadorRepository $jugadorRepository): JsonResponse {
        
        $club = $em->getRepository(Club::class)->find($clubId);
        if (!$club) {
            return new JsonResponse(['error' => 'Club no encontrado'], 404);
        }

        $nombre = $request->query->get('nombre');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $queryBuilder = $jugadorRepository->createQueryBuilder('j')
            ->where('j.club = :club')
            ->setParameter('club', $club);

        if ($nombre) {
            $queryBuilder->andWhere('j.nombre LIKE :nombre')
                ->setParameter('nombre', '%' . $nombre . '%');
        }

        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $jugadores = $queryBuilder->getQuery()->getResult();
        $jugadoresArray = [];

        foreach ($jugadores as $jugador) {
            $jugadoresArray[] = [
                'id' => $jugador->getId(),
                'nombre' => $jugador->getNombre(),
                'salario' => $jugador->getSalario(),
            ];
        }

        return new JsonResponse($jugadoresArray, 200);
    }



}
