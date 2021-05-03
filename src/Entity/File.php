<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @Groups({"main"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=255)
     */
    private $media_category;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=255)
     */
    private $media_type;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=512)
     */
    private $path;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=512)
     */
    private $full_path;
    
    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=255)
     */
    private $extension;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=255)
     */
    private $fileType;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="integer")
     */
    private $file_size;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=255)
     */
    private $file_system;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="datetime")
     */
    private $date_updated;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    /**
     * @Groups({"main"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mime_type;

    /**
     * @ORM\OneToMany(targetEntity=FileDownload::class, mappedBy="file", orphanRemoval=true)
     */
    private $fileDownloads;

    public function __construct()
    {
        $this->fileDownloads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
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

    public function getFileSystem(): ?string
    {
        return $this->file_system;
    }

    public function setFileSystem(string $file_system): self
    {
        $this->file_system = $file_system;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->file_size;
    }

    public function setFileSize(int $file_size): self
    {
        $this->file_size = $file_size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    public function setMimeType(string $mime_type): self
    {
        $this->mime_type = $mime_type;

        return $this;
    }

    /**
     * @return Collection|FileDownload[]
     */
    public function getFileDownloads(): Collection
    {
        return $this->fileDownloads;
    }

    public function addFileDownload(FileDownload $fileDownload): self
    {
        if (!$this->fileDownloads->contains($fileDownload)) {
            $this->fileDownloads[] = $fileDownload;
            $fileDownload->setFile($this);
        }

        return $this;
    }

    public function removeFileDownload(FileDownload $fileDownload): self
    {
        if ($this->fileDownloads->contains($fileDownload)) {
            $this->fileDownloads->removeElement($fileDownload);
            // set the owning side to null (unless already changed)
            if ($fileDownload->getFile() === $this) {
                $fileDownload->setFile(null);
            }
        }

        return $this;
    }

    public function getFullPath(): ?string
    {
        return $this->full_path;
    }

    public function setFullPath(string $full_path): self
    {
        $this->full_path = $full_path;

        return $this;
    }

    public function getMediaCategory(): ?string
    {
        return $this->media_category;
    }

    public function setMediaCategory(string $media_category): self
    {
        $this->media_category = $media_category;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->media_type;
    }

    public function setMediaType(string $media_type): self
    {
        $this->media_type = $media_type;

        return $this;
    }
}
