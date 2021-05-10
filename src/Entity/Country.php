<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2)
     * @Groups({"list"})
     */
    private $alpha_2;

    /**
     * @ORM\Column(type="string", length=3)
     * @Groups({"list"})
     */
    private $alpha_3;

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

    public function getAlpha2(): ?string
    {
        return $this->alpha_2;
    }

    public function setAlpha2(string $alpha_2): self
    {
        $this->alpha_2 = $alpha_2;

        return $this;
    }

    public function getAlpha3(): ?string
    {
        return $this->alpha_3;
    }

    public function setAlpha3(string $alpha_3): self
    {
        $this->alpha_3 = $alpha_3;

        return $this;
    }
}
