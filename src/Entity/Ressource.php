<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RessourceRepository::class)]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le type ")]
    private ?string $typeRessource = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir la desponibilite ")]
    private ?string $disponibiliteRessource = null;

    #[ORM\Column(length: 255)]
    private ?string $nomRessource = null;

    #[ORM\OneToMany(mappedBy: 'Ressource', targetEntity: Reservation::class)]
    private Collection $Reservation;

    public function __construct()
    {
        $this->Reservation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeRessource(): ?string
    {
        return $this->typeRessource;
    }

    public function setTypeRessource(string $typeRessource): self
    {
        $this->typeRessource = $typeRessource;

        return $this;
    }

    public function getDisponibiliteRessource(): ?string
    {
        return $this->disponibiliteRessource;
    }

    public function setDisponibiliteRessource(string $disponibiliteRessource): self
    {
        $this->disponibiliteRessource = $disponibiliteRessource;

        return $this;
    }

    public function getNomRessource(): ?string
    {
        return $this->nomRessource;
    }

    public function setNomRessource(string $nomRessource): self
    {
        $this->nomRessource = $nomRessource;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->Reservation;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->Reservation->contains($reservation)) {
            $this->Reservation->add($reservation);
            $reservation->setRessource($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->Reservation->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getRessource() === $this) {
                $reservation->setRessource(null);
            }
        }

        return $this;
    }
}
