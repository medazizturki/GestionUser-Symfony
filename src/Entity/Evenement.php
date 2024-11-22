<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Evenement")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"veuillez saisir l'image de l'evenement ")]
    private $imageEvenement = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le nom de l'evenement ")]
    #[Groups("Evenement")]
    private ?string $typeEvenement = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le nom de l'evenement ")]
    #[Groups("Evenement")]
    private ?string $nomEvenement = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le lieu de l'evenement ")]
    #[Groups("Evenement")]
    private ?string $lieuEvenement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Evenement")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Evenement")]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("Evenement")]
    private ?string $Color = null;

    #[ORM\OneToMany(mappedBy: 'Evenement', targetEntity: Participation::class)]
    #[Groups("Evenement")]
    private Collection $Participation;

    #[ORM\Column]
    private ?int $nb_participants = null;

    public function __construct()
    {
        $this->Participation = new ArrayCollection();
        $this->dateDebut = new \DateTime('now');
        $this->dateFin = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageEvenement()
    {
        return $this->imageEvenement;
    }

    public function setImageEvenement($imageEvenement)
    {
        $this->imageEvenement = $imageEvenement;

        return $this;
    }

    public function getTypeEvenement(): ?string
    {
        return $this->typeEvenement;
    }

    public function setTypeEvenement(string $typeEvenement): self
    {
        $this->typeEvenement = $typeEvenement;

        return $this;
    }

    public function getNomEvenement(): ?string
    {
        return $this->nomEvenement;
    }

    public function setNomEvenement(string $nomEvenement): self
    {
        $this->nomEvenement = $nomEvenement;

        return $this;
    }

    public function getLieuEvenement(): ?string
    {
        return $this->lieuEvenement;
    }

    public function setLieuEvenement(string $lieuEvenement): self
    {
        $this->lieuEvenement = $lieuEvenement;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->Color;
    }

    public function setColor(?string $Color): self
    {
        $this->Color = $Color;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipation(): Collection
    {
        return $this->Participation;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->Participation->contains($participation)) {
            $this->Participation->add($participation);
            $participation->setEvenement($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->Participation->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getEvenement() === $this) {
                $participation->setEvenement(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nomEvenement;
    }

    public function getNbrparticipants(): ?int
    {
        return $this->nb_participants;
    }

    public function setNbrparticipants(int $nb_participants): self
    {
        $this->nb_participants = $nb_participants;

        return $this;
    }


}
