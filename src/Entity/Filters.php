<?php

namespace App\Entity;

use App\Repository\FiltersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiltersRepository::class)]
class Filters
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'filters')]
    private ?Categories $categories = null;

    #[ORM\Column(length: 50)]
    private ?string $City = null;

    #[ORM\ManyToOne(inversedBy: 'filters')]
    private ?Events $Ville = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->City;
    }

    public function setCity(string $City): static
    {
        $this->City = $City;

        return $this;
    }

    public function getVille(): ?Events
    {
        return $this->Ville;
    }

    public function setVille(?Events $Ville): static
    {
        $this->Ville = $Ville;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
