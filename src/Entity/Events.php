<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventsRepository::class)]
class Events
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $quater = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $event_date = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Images::class, orphanRemoval: true, cascade:['persist'])]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $categorie = null;

    #[ORM\OneToMany(mappedBy: 'Ville', targetEntity: Filters::class)]
    private Collection $filters;


    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Participants::class, orphanRemoval: true)]
    private Collection $participants;

    #[ORM\OneToMany(mappedBy: 'events', targetEntity: Guests::class, orphanRemoval: true)]
    private Collection $guests;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->event_date = new \DateTimeImmutable();
        $this->created_at = new \DateTimeImmutable();
        $this->filters = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->guests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getEventDate(): ?\DateTimeImmutable
    {
        return $this->event_date;
    }

    public function setEventDate(\DateTimeImmutable $event_date): static
    {
        $this->event_date = $event_date;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setEvent($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEvent() === $this) {
                $comment->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setEvent($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getEvent() === $this) {
                $image->setEvent(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categories
    {
        return $this->categorie;
    }

    public function setCategorie(?Categories $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Filters>
     */
    public function getFilters(): Collection
    {
        return $this->filters;
    }

    public function addFilter(Filters $filter): static
    {
        if (!$this->filters->contains($filter)) {
            $this->filters->add($filter);
            $filter->setVille($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Participants>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participants $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setEvent($this);
        }

        return $this;
    }

    public function removeParticipant(Participants $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getEvent() === $this) {
                $participant->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Guests>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guests $guest): static
    {
        if (!$this->guests->contains($guest)) {
            $this->guests->add($guest);
            $guest->setEvents($this);
        }

        return $this;
    }

    public function removeGuest(Guests $guest): static
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getEvents() === $this) {
                $guest->setEvents(null);
            }
        }

        return $this;
    }

}
