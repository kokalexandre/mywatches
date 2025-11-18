<?php

namespace App\Entity;

use App\Repository\MontreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MontreRepository::class)]
class Montre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\ManyToOne(inversedBy: 'montres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Coffre $coffre = null;

    /**
     * @var Collection<int, Vitrine>
     */
    #[ORM\ManyToMany(targetEntity: Vitrine::class, mappedBy: 'montres')]
    private Collection $vitrines;

    public function __construct()
    {
        $this->vitrines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getCoffre(): ?Coffre
    {
        return $this->coffre;
    }

    public function setCoffre(?Coffre $coffre): static
    {
        $this->coffre = $coffre;

        return $this;
    }

    /**
     * @return Collection<int, Vitrine>
     */
    public function getVitrines(): Collection
    {
        return $this->vitrines;
    }

    public function addVitrine(Vitrine $vitrine): static
    {
        if (!$this->vitrines->contains($vitrine)) {
            $this->vitrines->add($vitrine);
            $vitrine->addMontre($this);
        }

        return $this;
    }

    public function removeVitrine(Vitrine $vitrine): static
    {
        if ($this->vitrines->removeElement($vitrine)) {
            $vitrine->removeMontre($this);
        }

        return $this;
    }

    public function __toString(): string
    {

        $parts = [];

        if ($this->marque) {
            $parts[] = $this->marque;
        }

        if ($this->reference) {
            $parts[] = $this->reference;
        }

        if ($this->description) {
            $parts[] = '– ' . $this->description;
        }

        $label = trim(implode(' ', $parts));

        if ($label === '') {
            $label = 'Montre #' . ($this->id ?? '?');
        }

        return $label;
    }
}