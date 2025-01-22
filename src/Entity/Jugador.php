<?php

namespace App\Entity;

use App\Repository\JugadorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JugadorRepository::class)]
class Jugador
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\Column(type: 'float', nullable: true)]
    private $salario;

    #[ORM\ManyToOne(targetEntity: Club::class, inversedBy: 'jugadores')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private $club;

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

    public function getSalario(): ?float
    {
        return $this->salario;
    }

    public function setSalario(?float $salario): self
    {
        $this->salario = $salario;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }
}

