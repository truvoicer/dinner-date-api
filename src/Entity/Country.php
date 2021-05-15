<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @Groups({"list", "members_list", "full_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "members_list", "full_user"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2)
     * @Groups({"list", "members_list", "full_user"})
     */
    private $alpha_2;

    /**
     * @ORM\Column(type="string", length=3)
     * @Groups({"list", "members_list", "full_user"})
     */
    private $alpha_3;

    /**
     * @ORM\OneToMany(targetEntity=UserProfile::class, mappedBy="country")
     */
    private $userProfiles;

    public function __construct()
    {
        $this->userProfiles = new ArrayCollection();
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

    /**
     * @return Collection|UserProfile[]
     */
    public function getUserProfiles(): Collection
    {
        return $this->userProfiles;
    }

    public function addUserProfile(UserProfile $userProfile): self
    {
        if (!$this->userProfiles->contains($userProfile)) {
            $this->userProfiles[] = $userProfile;
            $userProfile->setCountry($this);
        }

        return $this;
    }

    public function removeUserProfile(UserProfile $userProfile): self
    {
        if ($this->userProfiles->removeElement($userProfile)) {
            // set the owning side to null (unless already changed)
            if ($userProfile->getCountry() === $this) {
                $userProfile->setCountry(null);
            }
        }

        return $this;
    }
}
