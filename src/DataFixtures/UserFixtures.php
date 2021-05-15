<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\FileSystem;
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
use App\Service\Tools\FileSystem\Public\Upload\LocalPublicUploadService;
use App\Service\Tools\FileSystem\Public\Upload\LocalTempUploadService;
use App\Service\Tools\FileSystem\Public\Upload\S3PublicUploadService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
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
    const FILE_SYSTEMS = [
        [
            "name" => S3PublicUploadService::FILE_SYSTEM_NAME,
            "base_path" => null,
            "base_url" => "https://dinner-date-media-public.s3.eu-west-2.amazonaws.com",
        ],
        [
            "name" => LocalTempUploadService::FILE_SYSTEM_NAME,
            "base_path" => "/var/temp",
            "base_url" => null,
        ],
        [
            "name" => LocalPublicUploadService::FILE_SYSTEM_NAME,
            "base_path" => "/var/public",
            "base_url" => null,
        ],
    ];
    protected UserPasswordEncoderInterface $passwordEncoder;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
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

        $countriesSql = file_get_contents("/var/www/html/resources/sql/countries.sql");
        $this->entityManager->getConnection()->executeQuery($countriesSql);

        $countryRepo = $manager->getRepository(Country::class);
        $countriesArray = $countryRepo->findByParamsArray();

        foreach (self::MEMBERSHIPS as $name => $label) {
            $membershipModel = new Membership();
            $membershipModel->setDisplayName($label);
            $membershipModel->setName($name);
            $manager->persist($membershipModel);
        }
        $manager->flush();

        foreach (self::FILE_SYSTEMS as $system) {
            $fileSystemModel = new FileSystem();
            $fileSystemModel->setName($system["name"]);
            $fileSystemModel->setBasePath($system["base_path"]);
            $fileSystemModel->setBaseUrl($system["base_url"]);
            $manager->persist($fileSystemModel);
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

            $countryEntity = $countryRepo->find($countriesArray[mt_rand(0, count($countriesArray) - 1)]["id"]);
            $userProfile->setCountry($countryEntity);
            $userProfile->setEthnicity($countryEntity->getName());
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
