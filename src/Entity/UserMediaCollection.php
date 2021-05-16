<?php

namespace App\Entity;

use App\Repository\UserMediaCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserMediaCollectionRepository::class)
 */
class UserMediaCollection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"full_media"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MediaCollection::class, inversedBy="userMediaCollections")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"full_media"})
     */
    private $media_collection;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userMediaCollections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full_media"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full_media"})
     */
    private $label;

    /**
     * @ORM\ManyToMany(targetEntity=File::class, inversedBy="userMediaCollections")
     * @Groups({"full_media"})
     */
    private $file;

    public function __construct()
    {
        $this->file = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaCollection(): ?MediaCollection
    {
        return $this->media_collection;
    }

    public function setMediaCollection(?MediaCollection $media_collection): self
    {
        $this->media_collection = $media_collection;

        return $this;
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
     * @return Collection|File[]
     */
    public function getFile(): Collection
    {
        return $this->file;
    }

    public function addFile(File $file): self
    {
        if (!$this->file->contains($file)) {
            $this->file[] = $file;
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        $this->file->removeElement($file);

        return $this;
    }
}
