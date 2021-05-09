<?php

namespace App\DataFixtures;

use App\Entity\Membership;
use App\Entity\User;
use App\Entity\UserMembership;
use App\Entity\UserProfile;
use App\Library\Resources\TestData\BodyType;
use App\Library\Resources\TestData\EyeColor;
use App\Library\Resources\TestData\GenderPreferences;
use App\Library\Resources\TestData\Genders;
use App\Library\Resources\TestData\HairColor;
use App\Library\Resources\TestData\HeightUnits;
use App\Library\Resources\TestData\MaritalStatus;
use App\Library\Resources\TestData\SexualPreferences;
use App\Library\Resources\TestData\SmokingPreferences;
use App\Library\Resources\TestData\SmokingStatus;
use App\Library\Resources\TestData\WeightUnits;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    const MEMBERSHIPS = [
        "free_membership" => "Free Membership",
        "bronze_membership" => "Bronze Membership",
        "silver_membership" => "Silver Membership",
        "gold_membership" => "Gold Membership",
        "platinum_membership" => "Platinum Membership",
    ];
    protected UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $bodyTypes = BodyType::getData();
        $eyeColor = EyeColor::getData();
        $genders = Genders::getData();
        $genderPreferences = GenderPreferences::getData();
        $hairColor = HairColor::getData();
        $maritalStatus = MaritalStatus::getData();
        $sexualPreferences = SexualPreferences::getData();
        $smokingPreferences = SmokingPreferences::getData();
        $smokingStatus = SmokingStatus::getData();
        $weightUnits = WeightUnits::getData();
        $heightUnits = HeightUnits::getData();

        foreach (self::MEMBERSHIPS as $name => $label) {
            $membershipModel = new Membership();
            $membershipModel->setDisplayName($label);
            $membershipModel->setName($name);
            $manager->persist($membershipModel);
        }
        $manager->flush();

        $freeMembership = $manager->getRepository(Membership::class)->findOneBy(["name" => "free_membership"]);

        for ($i = 0; $i < 100; $i++) {
            $faker = \Faker\Factory::create();
            $nowDate = $faker->dateTimeBetween("-30 years");
            $firstName = $faker->firstName;
            $lastname = $faker->lastName;
            $country = $faker->country;
            if ($i === 0) {
                $username = "truvoice";
                $email = "mikydxl@gmail.com";
                $roles = ["ROLE_USER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"];
            } else {
                $username = strtolower($firstName) . "_" . strtolower($lastname);
                $email = strtolower($faker->email);
                $roles = ["ROLE_USER"];
            }

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, "Deelite4"));
            $user->setDateCreated($nowDate);
            $user->setDateUpdated($nowDate);
            $user->setRoles($roles);


            $userProfile = new UserProfile();
            $userProfile->setUser($user);
            $userProfile->setDob($faker->dateTimeBetween("-30 years", "-18 years"));
            $userProfile->setHeight(mt_rand(4, 7));
            $userProfile->setWeight(mt_rand(60, 150));

            $userProfile->setFirstName($firstName);
            $userProfile->setLastName($lastname);
            $userProfile->setAddress($faker->address);
            $userProfile->setBodyType($bodyTypes[mt_rand(0, count($bodyTypes) - 1)]);
            $userProfile->setCity($faker->city);
            $userProfile->setCountry($country);
            $userProfile->setEthnicity($country);
            $userProfile->setEyeColor($eyeColor[mt_rand(0, count($eyeColor) - 1)]);
            $userProfile->setGender($genders[mt_rand(0, count($genders) - 1)]);
            $userProfile->setGenderPreference($genderPreferences[mt_rand(0, count($genderPreferences) - 1)]);
            $userProfile->setHairColor($hairColor[mt_rand(0, count($hairColor) - 1)]);
            $userProfile->setMaritalStatus($maritalStatus[mt_rand(0, count($maritalStatus) - 1)]);
            $userProfile->setSexualPreference($sexualPreferences[mt_rand(0, count($sexualPreferences) - 1)]);
            $userProfile->setPartnerQualities(null);
            $userProfile->setInterests(null);
            $userProfile->setInterests(null);
            $userProfile->setHobbies(null);
            $userProfile->setSmokingPreference($smokingPreferences[mt_rand(0, count($smokingPreferences) - 1)]);
            $userProfile->setSmokingStatus($smokingStatus[mt_rand(0, count($smokingStatus) - 1)]);
            $userProfile->setLanguages(null);
            $userProfile->setHeightUnit($heightUnits[mt_rand(0, count($heightUnits) - 1)]);
            $userProfile->setWeightUnit($weightUnits[mt_rand(0, count($weightUnits) - 1)]);

            $userMembership = new UserMembership();
            $userMembership->setUser($user);
            $userMembership->setMembership($freeMembership);

            $manager->persist($user);
            $manager->persist($userMembership);
            $manager->persist($userProfile);

            $manager->flush();
        }
    }
}
