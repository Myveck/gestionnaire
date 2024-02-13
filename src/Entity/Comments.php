<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(options:['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $comment_date = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Events $event = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participants $participant = null;

    public function __construct()
    {
        $this->comment_date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCommentDate(): ?\DateTimeImmutable
    {
        return $this->comment_date;
    }

    public function setCommentDate(\DateTimeImmutable $comment_date): static
    {
        $this->comment_date = $comment_date;

        return $this;
    }

    public function getEvent(): ?Events
    {
        return $this->event;
    }

    public function setEvent(?Events $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getParticipant(): ?Participants
    {
        return $this->participant;
    }

    public function setParticipant(?Participants $participant): static
    {
        $this->participant = $participant;

        return $this;
    }
}
