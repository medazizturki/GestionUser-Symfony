<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("post:read")]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le nom ")]
    #[Groups("post:read")]

    private ?string $nomReclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le prenom ")]
    #[Groups("post:read")]

    private ?string $prenomReclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir la destination ")]
    #[Groups("post:read")]

    private ?string $destinationReclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir la description ")]
    #[Groups("post:read")]

    private ?string $descriptionReclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le type ")]
    #[Groups("post:read")]

    private ?string $typeReclamation = null;

    #[ORM\ManyToOne(inversedBy: 'Reclamation')]
    private ?User $User = null;

    #[ORM\OneToMany(mappedBy: 'Reclamation', targetEntity: Reponse::class)]
    private Collection $Reponse;

    public function __construct()
    {
        $this->Reponse = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomReclamation(): ?string
    {
        return $this->nomReclamation;
    }

    public function setNomReclamation(string $nomReclamation): self
    {
        $this->nomReclamation = $nomReclamation;

        return $this;
    }

    public function getPrenomReclamation(): ?string
    {
        return $this->prenomReclamation;
    }

    public function setPrenomReclamation(string $prenomReclamation): self
    {
        $this->prenomReclamation = $prenomReclamation;

        return $this;
    }

    public function getDestinationReclamation(): ?string
    {
        return $this->destinationReclamation;
    }

    public function setDestinationReclamation(string $destinationReclamation): self
    {
        $this->destinationReclamation = $destinationReclamation;

        return $this;
    }

    public function getDescriptionReclamation(): ?string
    {
        return $this->descriptionReclamation;
    }

    public function setDescriptionReclamation(string $descriptionReclamation): self
    {
        $this->descriptionReclamation = $descriptionReclamation;

        return $this;
    }

    public function getTypeReclamation(): ?string
    {
        return $this->typeReclamation;
    }

    public function setTypeReclamation(string $typeReclamation): self
    {
        $this->typeReclamation = $typeReclamation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponse(): Collection
    {
        return $this->Reponse;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->Reponse->contains($reponse)) {
            $this->Reponse->add($reponse);
            $reponse->setReclamation($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->Reponse->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getReclamation() === $this) {
                $reponse->setReclamation(null);
            }
        }

        return $this;
    }
}
