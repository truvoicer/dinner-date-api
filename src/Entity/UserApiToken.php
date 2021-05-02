<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\UserApiTokenRepository;

/**
 * @ORM\Entity(repositoryClass=UserApiTokenRepository::class)
 */
class UserApiToken
{
    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="apiTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(User $user, string $type)
    {
        $this->token = bin2hex(random_bytes(60));
        $this->user = $user;
        $this->expiresAt = new \DateTime('+1 days');
        $this->setType($type);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
    }

    public function renewExpiresAt()
    {
        $this->expiresAt = new \DateTime('+30 days');
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function isExpired(): bool
    {
        return $this->getExpiresAt() >= new \DateTime();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        switch ($type) {
            case "user":
                $this->type = "user";
                break;
            default:
                $this->type = "auto";
                break;

        }
        return $this;
    }
}
