<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Library\Resources\TestData\BodyType;
use App\Library\Resources\TestData\EyeColor;
use App\Library\Resources\TestData\Genders;
use App\Library\Resources\TestData\HairColor;
use App\Library\Resources\TestData\MaritalStatus;
use App\Library\Resources\TestData\SexualPreferences;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
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
        $hairColor = HairColor::getData();
        $maritalStatus = MaritalStatus::getData();
        $sexualPreferences = SexualPreferences::getData();

        for ($i = 0; $i < 100; $i++) {
            $faker = \Faker\Factory::create();
            $nowDate = $faker->dateTimeBetween("-30 years");
            $firstName = $faker->firstName;
            $lastname = $faker->lastName;
            $country = $faker->country;

            $user = new User();
            $user->setUsername(strtolower($firstName)."_".strtolower($lastname));
            $user->setEmail(strtolower($faker->email));
            $user->setPassword($this->passwordEncoder->encodePassword($user, "Deelite4"));
            $user->setDateCreated($nowDate);
            $user->setDateUpdated($nowDate);
            $user->setRoles(["ROLE_USER"]);

            $userProfile = new UserProfile();
            $userProfile->setUser($user);
            $userProfile->setDob($faker->dateTimeBetween("-30 years", "-18 years"));
            $userProfile->setHeight(mt_rand(4, 7) . " ft");
            $userProfile->setWeight(mt_rand(60, 150) . " kg");

            $userProfile->setFirstName($firstName);
            $userProfile->setLastName($lastname);
            $userProfile->setAddress($faker->address);
            $userProfile->setBodyType($bodyTypes[mt_rand(0, count($bodyTypes) - 1)]);
            $userProfile->setCity($faker->city);
            $userProfile->setCountry($country);
            $userProfile->setEthnicity($country);
            $userProfile->setEyeColor($eyeColor[mt_rand(0, count($eyeColor) - 1)]);
            $userProfile->setGender($genders[mt_rand(0, count($genders) - 1)]);
            $userProfile->setHairColor($hairColor[mt_rand(0, count($hairColor) - 1)]);
            $userProfile->setMaritalStatus($maritalStatus[mt_rand(0, count($maritalStatus) - 1)]);
            $userProfile->setSexualPreference($sexualPreferences[mt_rand(0, count($sexualPreferences) - 1)]);

            $manager->persist($user);
            $manager->persist($userProfile);

            $manager->flush();
        }
    }
}
