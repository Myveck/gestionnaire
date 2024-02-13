<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Events::class, orphanRemoval: true)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'categories', targetEntity: Filters::class)]
    private Collection $filters;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->filters = new ArrayCollection();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Events $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCategorie($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCategorie() === $this) {
                $event->setCategorie(null);
            }
        }

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
            $filter->setCategories($this);
        }

        return $this;
    }

    public function removeFilter(Filters $filter): static
    {
        if ($this->filters->removeElement($filter)) {
            // set the owning side to null (unless already changed)
            if ($filter->getCategories() === $this) {
                $filter->setCategories(null);
            }
        }

        return $this;
    }
}
