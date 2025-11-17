<?php

namespace App\Entity;

use App\Repository\CoffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoffreRepository::class)]
class Coffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Montre>
     */
    #[ORM\OneToMany(targetEntity: Montre::class, mappedBy: 'coffre', orphanRemoval: true)]
    private Collection $montres;

    #[ORM\OneToOne(inversedBy: 'coffre', cascade: ['persist', 'remove'])]
    private ?Member $member = null;

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
            $montre->setCoffre($this);
        }

        return $this;
    }

    public function removeMontre(Montre $montre): static
    {
        if ($this->montres->removeElement($montre)) {
            if ($montre->getCoffre() === $this) {
                $montre->setCoffre(null);
            }
        }

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function __toString(): string
    {
        if ($this->description) {
            return $this->description;
        }

        if ($this->id !== null) {
            return 'Coffre n°'.$this->id;
        }

        return 'Coffre (non sauvegardé)';
    }

}
