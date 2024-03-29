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
    private $gender_preference;

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
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $address;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $height;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
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
    private $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $partner_qualities;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $interests;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $hobbies;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $smoking_preference;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $smoking_status;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $languages;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $height_unit;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Groups({"members_list", "full_user"})
     */
    private $weight_unit;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="user_profile", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="userProfiles")
     * @Groups({"members_list", "full_user"})
     */
    private $country;


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

    public function getGenderPreference(): ?string
    {
        return $this->gender_preference;
    }

    public function setGenderPreference(?string $gender_preference): self
    {
        $this->gender_preference = $gender_preference;

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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

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

    public function getPartnerQualities(): ?string
    {
        return $this->partner_qualities;
    }

    public function setPartnerQualities(?string $partner_qualities): self
    {
        $this->partner_qualities = $partner_qualities;

        return $this;
    }

    public function getInterests(): ?string
    {
        return $this->interests;
    }

    public function setInterests(?string $interests): self
    {
        $this->interests = $interests;

        return $this;
    }

    public function getHobbies(): ?string
    {
        return $this->hobbies;
    }

    public function setHobbies(?string $hobbies): self
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    public function getSmokingPreference(): ?string
    {
        return $this->smoking_preference;
    }

    public function setSmokingPreference(?string $smoking_preference): self
    {
        $this->smoking_preference = $smoking_preference;

        return $this;
    }

    public function getSmokingStatus(): ?string
    {
        return $this->smoking_status;
    }

    public function setSmokingStatus(?string $smoking_status): self
    {
        $this->smoking_status = $smoking_status;

        return $this;
    }

    public function getLanguages(): ?string
    {
        return $this->languages;
    }

    public function setLanguages(?string $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getHeightUnit(): ?string
    {
        return $this->height_unit;
    }

    public function setHeightUnit(?string $height_unit): self
    {
        $this->height_unit = $height_unit;

        return $this;
    }

    public function getWeightUnit(): ?string
    {
        return $this->weight_unit;
    }

    public function setWeightUnit(?string $weight_unit): self
    {
        $this->weight_unit = $weight_unit;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}
