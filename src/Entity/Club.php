<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\Column(type: 'float')]
    private $presupuesto;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Jugador::class, cascade: ['persist', 'remove'])]
    private $jugadores;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Entrenador::class, cascade: ['persist', 'remove'])]
    private $entrenadores;

    public function __construct()
    {
        $this->jugadores = new ArrayCollection();
        $this->entrenadores = new ArrayCollection();
    }

    // Getters y Setters para cada propiedad

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getPresupuesto(): ?float
    {
        return $this->presupuesto;
    }

    public function setPresupuesto(float $presupuesto): self
    {
        $this->presupuesto = $presupuesto;

        return $this;
    }

    public function getJugadores(): Collection
    {
        return $this->jugadores;
    }

    public function getEntrenadores(): Collection
    {
        return $this->entrenadores;
    }
}

