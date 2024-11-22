<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank (message:"veuillez saisir le nombre de science ")]
    private ?int $nb_science = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"veuillez saisir le type de paiement")]
    private ?string $typeDePaiement = null;

    #[ORM\ManyToOne(inversedBy: 'Facture')]
    private ?Rendezvous $Rendezvous = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbScience(): ?int
    {
        return $this->nb_science;
    }

    public function setNbScience(int $nb_science): self
    {
        $this->nb_science = $nb_science;

        return $this;
    }

    public function getTypeDePaiement(): ?string
    {
        return $this->typeDePaiement;
    }

    public function setTypeDePaiement(string $typeDePaiement): self
    {
        $this->typeDePaiement = $typeDePaiement;

        return $this;
    }

    public function getRendezvous(): ?Rendezvous
    {
        return $this->Rendezvous;
    }

    public function setRendezvous(?Rendezvous $Rendezvous): self
    {
        $this->Rendezvous = $Rendezvous;

        return $this;
    }
}
