<?php

namespace App\Entity;

use App\Repository\StudentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentsRepository::class)]
class Students
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $firstName = null;

    #[ORM\Column(length: 30)]
    private ?string $lastName = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    private ?string $city = null;

    #[ORM\Column(length: 50)]
    private ?string $quater = null;

    #[ORM\Column]
    private ?int $study_year = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $birthday = null;

    #[ORM\Column(length: 30)]
    private ?string $sector = null;

    #[ORM\Column(length: 30)]
    private ?string $study_option = null;

    #[ORM\Column(length: 30)]
    private ?string $telephone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getQuater(): ?string
    {
        return $this->quater;
    }

    public function setQuater(string $quater): static
    {
        $this->quater = $quater;

        return $this;
    }

    public function getStudyYear(): ?int
    {
        return $this->study_year;
    }

    public function setStudyYear(int $study_year): static
    {
        $this->study_year = $study_year;

        return $this;
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeImmutable $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(string $sector): static
    {
        $this->sector = $sector;

        return $this;
    }

    public function getStudyOption(): ?string
    {
        return $this->study_option;
    }

    public function setStudyOption(string $study_option): static
    {
        $this->study_option = $study_option;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }
}
