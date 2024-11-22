<?php

namespace App\Entity;

use App\Repository\RendezvousRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RendezvousRepository::class)]
class Rendezvous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:3)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le nom ")]
    private ?string $nomRendezvous = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:3)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le prenom ")]
    private ?string $prenomRendezvous = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le lieu ")]
    private ?string $lieuRendezvous = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"veuillez saisir l'email ")]
    #[Assert\Email]
    private ?string $emailRendezvous = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateRendezvous = null;

    #[ORM\Column(length: 255)]
    private ?string $Color = null;

    #[ORM\ManyToOne(inversedBy: 'Rendezvous')]
    private ?User $User = null;

    #[ORM\OneToMany(mappedBy: 'Rendezvous', targetEntity: Facture::class)]
    private Collection $Facture;

    public function __construct()
    {
        $this->Facture = new ArrayCollection();
        $this->dateRendezvous = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomRendezvous(): ?string
    {
        return $this->nomRendezvous;
    }

    public function setNomRendezvous(string $nomRendezvous): self
    {
        $this->nomRendezvous = $nomRendezvous;

        return $this;
    }

    public function getPrenomRendezvous(): ?string
    {
        return $this->prenomRendezvous;
    }

    public function setPrenomRendezvous(string $prenomRendezvous): self
    {
        $this->prenomRendezvous = $prenomRendezvous;

        return $this;
    }

    public function getLieuRendezvous(): ?string
    {
        return $this->lieuRendezvous;
    }

    public function setLieuRendezvous(string $lieuRendezvous): self
    {
        $this->lieuRendezvous = $lieuRendezvous;

        return $this;
    }

    public function getEmailRendezvous(): ?string
    {
        return $this->emailRendezvous;
    }

    public function setEmailRendezvous(string $emailRendezvous): self
    {
        $this->emailRendezvous = $emailRendezvous;

        return $this;
    }

    public function getDateRendezvous(): ?\DateTimeInterface
    {
        return $this->dateRendezvous;
    }

    public function setDateRendezvous(\DateTimeInterface $dateRendezvous): self
    {
        $this->dateRendezvous = $dateRendezvous;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->Color;
    }

    public function setColor(string $Color): self
    {
        $this->Color = $Color;

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
     * @return Collection<int, Facture>
     */
    public function getFacture(): Collection
    {
        return $this->Facture;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->Facture->contains($facture)) {
            $this->Facture->add($facture);
            $facture->setRendezvous($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->Facture->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getRendezvous() === $this) {
                $facture->setRendezvous(null);
            }
        }

        return $this;
    }
}
