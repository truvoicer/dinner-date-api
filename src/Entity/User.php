<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"members_list", "single"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"single"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "single"})
     */
    private $display_name;

    /**
     * @ORM\Column(type="json")
     * @Groups({"single"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"members_list", "single"})
     */
    private $date_updated;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"members_list", "single"})
     */
    private $date_created;

    /**
     * @ORM\OneToMany(targetEntity=UserProfile::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"members_list"})
     */
    private $userProfiles;

    /**
     * @ORM\OneToMany(targetEntity=UserPermission::class, mappedBy="user", orphanRemoval=true)
     */
    private $userPermissions;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserApiToken", mappedBy="user", orphanRemoval=true)
     */
    private $userApiTokens;

    /**
     * @ORM\OneToMany(targetEntity=UserMembership::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"members_list", "single"})
     */
    private $userMemberships;

    public function __construct()
    {
        $this->userProfiles = new ArrayCollection();
        $this->userPermissions = new ArrayCollection();
        $this->userMemberships = new ArrayCollection();
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
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayName(?string $display_name): self
    {
        $this->display_name = $display_name;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
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

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->date_updated;
    }

    public function setDateUpdated(\DateTimeInterface $date_updated): self
    {
        $this->date_updated = $date_updated;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    /**
     * @return Collection|UserProfile[]
     */
    public function getUserProfiles(): Collection
    {
        return $this->userProfiles;
    }

    public function addUserProfile(UserProfile $userProfile): self
    {
        if (!$this->userProfiles->contains($userProfile)) {
            $this->userProfiles[] = $userProfile;
            $userProfile->setUser($this);
        }

        return $this;
    }

    public function removeUserProfile(UserProfile $userProfile): self
    {
        if ($this->userProfiles->removeElement($userProfile)) {
            // set the owning side to null (unless already changed)
            if ($userProfile->getUser() === $this) {
                $userProfile->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserPermission[]
     */
    public function getUserPermissions(): Collection
    {
        return $this->userPermissions;
    }

    public function addUserPermission(UserPermission $userPermission): self
    {
        if (!$this->userPermissions->contains($userPermission)) {
            $this->userPermissions[] = $userPermission;
            $userPermission->setUser($this);
        }

        return $this;
    }

    public function removeUserPermission(UserPermission $userPermission): self
    {
        if ($this->userPermissions->removeElement($userPermission)) {
            // set the owning side to null (unless already changed)
            if ($userPermission->getUser() === $this) {
                $userPermission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserApiToken[]
     */
    public function getApiTokens(): Collection
    {
        return $this->userApiTokens;
    }

    public function addUserApiToken(UserApiToken $userApiToken): self
    {
        if (!$this->userApiTokens->contains($userApiToken)) {
            $this->userApiTokens[] = $userApiToken;
            $userApiToken->setUser($this);
        }

        return $this;
    }

    public function removeApiToken(UserApiToken $userApiToken): self
    {
        if ($this->userApiTokens->contains($userApiToken)) {
            $this->userApiTokens->removeElement($userApiToken);
            // set the owning side to null (unless already changed)
            if ($userApiToken->getUser() === $this) {
                $userApiToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserMembership[]
     */
    public function getUserMemberships(): Collection
    {
        return $this->userMemberships;
    }

    public function addUserMembership(UserMembership $userMembership): self
    {
        if (!$this->userMemberships->contains($userMembership)) {
            $this->userMemberships[] = $userMembership;
            $userMembership->setUser($this);
        }

        return $this;
    }

    public function removeUserMembership(UserMembership $userMembership): self
    {
        if ($this->userMemberships->removeElement($userMembership)) {
            // set the owning side to null (unless already changed)
            if ($userMembership->getUser() === $this) {
                $userMembership->setUser(null);
            }
        }

        return $this;
    }
}
