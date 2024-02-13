<?php

namespace App\Entity;

use App\Repository\GuestsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GuestsRepository::class)]
class Guests
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $guest_name = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'guests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Events $events = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuestName(): ?string
    {
        return $this->guest_name;
    }

    public function setGuestName(string $guest_name): static
    {
        $this->guest_name = $guest_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEvents(): ?Events
    {
        return $this->events;
    }

    public function setEvents(?Events $events): static
    {
        $this->events = $events;

        return $this;
    }
}
