<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5)]
    #[Assert\Length(max:20)]
    #[Assert\NotBlank (message:"veuillez saisir le nom de l'offre ")]
    private ?string $nomOffre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datepubOffre = null;

    #[ORM\OneToMany(mappedBy: 'Offre', targetEntity: Demande::class)]
    private Collection $Demande;

    public function __construct()
    {
        $this->Demande = new ArrayCollection();
        $this->datepubOffre = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomOffre(): ?string
    {
        return $this->nomOffre;
    }

    public function setNomOffre(string $nomOffre): self
    {
        $this->nomOffre = $nomOffre;

        return $this;
    }

    public function getDatepubOffre(): ?\DateTimeInterface
    {
        return $this->datepubOffre;
    }

    public function setDatepubOffre(\DateTimeInterface $datepubOffre): self
    {
        $this->datepubOffre = $datepubOffre;

        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemande(): Collection
    {
        return $this->Demande;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->Demande->contains($demande)) {
            $this->Demande->add($demande);
            $demande->setOffre($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->Demande->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getOffre() === $this) {
                $demande->setOffre(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nomOffre;
    }
}
