<?php

namespace App\DataFixtures;

use App\Entity\Club;
use App\Entity\Entrenador;
use App\Entity\Jugador;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Insertar clubes
        $club1 = new Club();
        $club1->setNombre('Real Madrid');
        $club1->setPresupuesto(11000);
        $manager->persist($club1);

        $club2 = new Club();
        $club2->setNombre('FC Barcelona');
        $club2->setPresupuesto(5000);
        $manager->persist($club2);

        $club3 = new Club();
        $club3->setNombre('Sevilla FC');
        $club3->setPresupuesto(5000000);
        $manager->persist($club3);

        // Insertar entrenadores
        $entrenador1 = new Entrenador();
        $entrenador1->setClub(null);
        $entrenador1->setNombre('Entrenador 1');
        $entrenador1->setSalario(null);
        $entrenador1->setEmail('uno@entrenador.com');
        $manager->persist($entrenador1);

        $entrenador2 = new Entrenador();
        $entrenador2->setClub(null);
        $entrenador2->setNombre('Entrenador 2');
        $entrenador2->setSalario(null);
        $entrenador2->setEmail('dos@entrenador.com');
        $manager->persist($entrenador2);

        $entrenador3 = new Entrenador();
        $entrenador3->setClub(null);
        $entrenador3->setNombre('Entrenador 3');
        $entrenador3->setSalario(null);
        $entrenador3->setEmail(null);
        $manager->persist($entrenador3);

        // Insertar jugadores
        $jugador1 = new Jugador();
        $jugador1->setClub(null);
        $jugador1->setNombre('Jugador 1');
        $jugador1->setSalario(null);
        $jugador1->setEmail('uno@jugador.com');
        $manager->persist($jugador1);

        $jugador2 = new Jugador();
        $jugador2->setClub(null);
        $jugador2->setNombre('Jugador 2');
        $jugador2->setSalario(null);
        $jugador2->setEmail('dos@jugador.com');
        $manager->persist($jugador2);

        $jugador3 = new Jugador();
        $jugador3->setClub(null);
        $jugador3->setNombre('Jugador 3');
        $jugador3->setSalario(null);
        $jugador3->setEmail('tres@jugador.com');
        $manager->persist($jugador3);

        $jugador4 = new Jugador();
        $jugador4->setClub(null);
        $jugador4->setNombre('Jugador 4');
        $jugador4->setSalario(null);
        $jugador4->setEmail(null);
        $manager->persist($jugador4);

        // Guardar los datos en la base de datos
        $manager->flush();
    }
}

