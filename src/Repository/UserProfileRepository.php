<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Service\Tools\UtilsService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UserProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProfile[]    findAll()
 * @method UserProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfile::class);
    }

    public function findOneUserProfile(User|UserInterface $user, array $params = [])
    {
        $query = $this->createQueryBuilder('user_profile')
            ->where("user_profile.user = :user")
            ->setParameter("user", $user);
        return $query->getQuery()->getResult();
    }

    public function getUserProfileObject(UserProfile $userProfile, array $data)
    {
        foreach ($data as $key => $value) {
            $setMethodName = sprintf("set%s", UtilsService::stringToCamelCase($key, true));
            if (method_exists($userProfile, $setMethodName)) {
                if ($key === "dob") {
                    $value = new \DateTime($value);
                }
                $userProfile->$setMethodName($value);
            }
        }
        return $userProfile;
    }

    public function createUserProfile(UserProfile $userProfile)
    {
        $this->getEntityManager()->persist($userProfile);
        $this->getEntityManager()->flush();
        return $userProfile;
    }

    public function updateUserProfile(UserProfile $userProfile)
    {
        $this->getEntityManager()->persist($userProfile);
        $this->getEntityManager()->flush();
        return $userProfile;
    }

    public function deleteUserProfile(UserProfile $userProfile) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($userProfile);
        $entityManager->flush();
        return $userProfile;
    }

}
