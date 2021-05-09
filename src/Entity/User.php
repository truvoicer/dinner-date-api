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
     * @Groups({"members_list", "single", "full_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"single", "full_user"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"members_list", "single", "full_user"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"single", "full_user"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"members_list", "single", "full_user"})
     */
    private $date_updated;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"members_list", "single", "full_user"})
     */
    private $date_created;

    /**
     * @ORM\OneToMany(targetEntity=UserPermission::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"members_list", "full_user"})
     */
    private $userPermissions;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserApiToken", mappedBy="user", orphanRemoval=true)
     */
    private $userApiTokens;

    /**
     * @ORM\OneToMany(targetEntity=UserMembership::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"members_list", "single", "full_user"})
     */
    private $userMemberships;

    /**
     * @ORM\OneToOne(targetEntity=UserProfile::class, inversedBy="user")
     * @Groups({"members_list", "full_user"})
     */
    private $user_profile;

    public function __construct()
    {
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

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

    public function getUserProfile(): ?UserProfile
    {
        return $this->user_profile;
    }

    public function setUserProfile(?UserProfile $user_profile): self
    {
        $this->user_profile = $user_profile;

        return $this;
    }
}
