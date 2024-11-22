<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("users")]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups("users")]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private ?string $email = null;


    #[ORM\Column]
    #[Groups("users")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups("users")]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups("users")]
    private ?int $telephone = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Participation::class)]
    private Collection $User;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Rendezvous::class)]
    private Collection $Rendezvous;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Demande::class)]
    private Collection $Demande;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Reservation::class)]
    private Collection $Reservation;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Reclamation::class)]
    private Collection $Reclamation;

    public function __construct()
    {
        $this->User = new ArrayCollection();
        $this->Rendezvous = new ArrayCollection();
        $this->Demande = new ArrayCollection();
        $this->Reservation = new ArrayCollection();
        $this->Reclamation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
  
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        #$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

public function __toSting(){

return $this->email;
}

/**
 * @return Collection<int, Participation>
 */
public function getUser(): Collection
{
    return $this->User;
}

public function addUser(Participation $user): self
{
    if (!$this->User->contains($user)) {
        $this->User->add($user);
        $user->setUser($this);
    }

    return $this;
}

public function removeUser(Participation $user): self
{
    if ($this->User->removeElement($user)) {
        // set the owning side to null (unless already changed)
        if ($user->getUser() === $this) {
            $user->setUser(null);
        }
    }

    return $this;
}

public function __toString()
{
    return $this->email;
}

/**
 * @return Collection<int, Rendezvous>
 */
public function getRendezvous(): Collection
{
    return $this->Rendezvous;
}

public function addRendezvou(Rendezvous $rendezvou): self
{
    if (!$this->Rendezvous->contains($rendezvou)) {
        $this->Rendezvous->add($rendezvou);
        $rendezvou->setUser($this);
    }

    return $this;
}

public function removeRendezvou(Rendezvous $rendezvou): self
{
    if ($this->Rendezvous->removeElement($rendezvou)) {
        // set the owning side to null (unless already changed)
        if ($rendezvou->getUser() === $this) {
            $rendezvou->setUser(null);
        }
    }

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
        $demande->setUser($this);
    }

    return $this;
}

public function removeDemande(Demande $demande): self
{
    if ($this->Demande->removeElement($demande)) {
        // set the owning side to null (unless already changed)
        if ($demande->getUser() === $this) {
            $demande->setUser(null);
        }
    }

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
        $reservation->setUser($this);
    }

    return $this;
}

public function removeReservation(Reservation $reservation): self
{
    if ($this->Reservation->removeElement($reservation)) {
        // set the owning side to null (unless already changed)
        if ($reservation->getUser() === $this) {
            $reservation->setUser(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Reclamation>
 */
public function getReclamation(): Collection
{
    return $this->Reclamation;
}

public function addReclamation(Reclamation $reclamation): self
{
    if (!$this->Reclamation->contains($reclamation)) {
        $this->Reclamation->add($reclamation);
        $reclamation->setUser($this);
    }

    return $this;
}

public function removeReclamation(Reclamation $reclamation): self
{
    if ($this->Reclamation->removeElement($reclamation)) {
        // set the owning side to null (unless already changed)
        if ($reclamation->getUser() === $this) {
            $reclamation->setUser(null);
        }
    }

    return $this;
}


}
