<?php

namespace App\Entity;

use App\Repository\FileSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FileSystemRepository::class)
 */
class FileSystem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"members_list", "full_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $base_path;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $base_url;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="file_system")
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
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

    public function getBasePath(): ?string
    {
        return $this->base_path;
    }

    public function setBasePath(?string $base_path): self
    {
        $this->base_path = $base_path;

        return $this;
    }

    public function getBaseUrl(): ?string
    {
        return $this->base_url;
    }

    public function setBaseUrl(?string $base_url): self
    {
        $this->base_url = $base_url;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setFileSystem($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getFileSystem() === $this) {
                $file->setFileSystem(null);
            }
        }

        return $this;
    }
}
