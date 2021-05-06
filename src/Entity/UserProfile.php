<?php

namespace App\Entity;

use App\Repository\UserProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserProfileRepository::class)
 */
class UserProfile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"members_list", "full_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $marital_status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $dob;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"members_list", "full_user"})
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $weight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $hair_color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $eye_color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $body_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $ethnicity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $sexual_preference;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="user_profile", cascade={"persist", "remove"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getMaritalStatus(): ?string
    {
        return $this->marital_status;
    }

    public function setMaritalStatus(?string $marital_status): self
    {
        $this->marital_status = $marital_status;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(?\DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHairColor(): ?string
    {
        return $this->hair_color;
    }

    public function setHairColor(?string $hair_color): self
    {
        $this->hair_color = $hair_color;

        return $this;
    }

    public function getEyeColor(): ?string
    {
        return $this->eye_color;
    }

    public function setEyeColor(?string $eye_color): self
    {
        $this->eye_color = $eye_color;

        return $this;
    }

    public function getBodyType(): ?string
    {
        return $this->body_type;
    }

    public function setBodyType(?string $body_type): self
    {
        $this->body_type = $body_type;

        return $this;
    }

    public function getEthnicity(): ?string
    {
        return $this->ethnicity;
    }

    public function setEthnicity(?string $ethnicity): self
    {
        $this->ethnicity = $ethnicity;

        return $this;
    }

    public function getSexualPreference(): ?string
    {
        return $this->sexual_preference;
    }

    public function setSexualPreference(?string $sexual_preference): self
    {
        $this->sexual_preference = $sexual_preference;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setUserProfile(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getUserProfile() !== $this) {
            $user->setUserProfile($this);
        }

        $this->user = $user;

        return $this;
    }
}
