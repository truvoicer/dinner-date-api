<?php

namespace App\Entity;

use App\Repository\FileDownloadRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FileDownloadRepository::class)
 * @UniqueEntity("download_key")
 */
class FileDownload
{
    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\ManyToOne(targetEntity=File::class, inversedBy="fileDownloads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $download_key;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getDownloadKey(): ?string
    {
        return $this->download_key;
    }

    public function setDownloadKey(string $download_key): self
    {
        $this->download_key = $download_key;

        return $this;
    }
}
