<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PermissionRepository::class)
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"main", "main_relations", "search", "list", "single"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"main", "main_relations", "search", "list", "single"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"main", "main_relations", "search", "list", "single"})
     */
    private $label;

    /**
     * @ORM\ManyToMany(targetEntity=UserPermission::class, mappedBy="permission")
     */
    private $userPermissions;

    public function __construct()
    {
        $this->userPermissions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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
            $userPermission->addPermission($this);
        }

        return $this;
    }

    public function removeUserPermission(UserPermission $userPermission): self
    {
        if ($this->userPermissions->removeElement($userPermission)) {
            $userPermission->removePermission($this);
        }

        return $this;
    }
}
