<?php

namespace App\Entity;

use App\Repository\MediaCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MediaCollectionRepository::class)
 */
class MediaCollection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"full_media"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full_media"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full_media"})
     */
    private $display_name;

    /**
     * @ORM\OneToMany(targetEntity=UserMediaCollection::class, mappedBy="media_collection", orphanRemoval=true)
     */
    private $userMediaCollections;

    public function __construct()
    {
        $this->userMediaCollections = new ArrayCollection();
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

    public function getDisplayname(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayname(string $display_name): self
    {
        $this->display_name = $display_name;

        return $this;
    }

    /**
     * @return Collection|UserMediaCollection[]
     */
    public function getUserMediaCollections(): Collection
    {
        return $this->userMediaCollections;
    }

    public function addUserMediaCollection(UserMediaCollection $userMediaCollection): self
    {
        if (!$this->userMediaCollections->contains($userMediaCollection)) {
            $this->userMediaCollections[] = $userMediaCollection;
            $userMediaCollection->setMediaCollection($this);
        }

        return $this;
    }

    public function removeUserMediaCollection(UserMediaCollection $userMediaCollection): self
    {
        if ($this->userMediaCollections->removeElement($userMediaCollection)) {
            // set the owning side to null (unless already changed)
            if ($userMediaCollection->getMediaCollection() === $this) {
                $userMediaCollection->setMediaCollection(null);
            }
        }

        return $this;
    }
}
