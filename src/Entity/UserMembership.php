<?php

namespace App\Entity;

use App\Repository\UserMembershipRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserMembershipRepository::class)
 */
class UserMembership
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"members_list"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userMemberships")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"members_list"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Membership::class, inversedBy="userMemberships")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"members_list"})
     */
    private $membership;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    public function setMembership(?Membership $membership): self
    {
        $this->membership = $membership;

        return $this;
    }
}
