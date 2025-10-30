<?php

namespace App\Entity;

use App\Repository\VitrineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VitrineRepository::class)]
class Vitrine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $publiee = null;

    #[ORM\ManyToOne(inversedBy: 'vitrines')]
    private ?Member $createur = null;

    /**
     * @var Collection<int, Montre>
     */
    #[ORM\ManyToMany(targetEntity: Montre::class, inversedBy: 'vitrines')]
    private Collection $montres;

    public function __construct()
    {
        $this->montres = new ArrayCollection();
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

    public function isPubliee(): ?bool
    {
        return $this->publiee;
    }

    public function setPubliee(bool $publiee): static
    {
        $this->publiee = $publiee;

        return $this;
    }

    public function getCreateur(): ?Member
    {
        return $this->createur;
    }

    public function setCreateur(?Member $createur): static
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * @return Collection<int, Montre>
     */
    public function getMontres(): Collection
    {
        return $this->montres;
    }

    public function addMontre(Montre $montre): static
    {
        if (!$this->montres->contains($montre)) {
            $this->montres->add($montre);
        }

        return $this;
    }

    public function removeMontre(Montre $montre): static
    {
        $this->montres->removeElement($montre);

        return $this;
    }
}
