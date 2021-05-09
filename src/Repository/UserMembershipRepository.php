<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserMembership;
use App\Repository\Helpers\RepositoryHelpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMembership|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMembership|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMembership[]    findAll()
 * @method UserMembership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMembership::class);
    }

    /**
     * @return UserMembership[] Returns an array of UserMembership objects
     */
    public function findMembersByMembership(string $membershipName, array $params = [])
    {
        $query = $this->createQueryBuilder('user_membership')
            ->leftJoin("user_membership.membership", "membership")
            ->where("membership.name = :membershipName")
            ->setParameter('membershipName', $membershipName);
        $query = RepositoryHelpers::addQueryBuilderConditions($query, $params);
        return $query->getQuery()->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findAllUsersWithMembership(array $params = [])
    {
        $query = RepositoryHelpers::addQueryBuilderConditions(
            $this->createQueryBuilder('user_membership'),
            $params
        );
        return array_map(function (UserMembership $userMembership) {
            return $userMembership->getUser();
        }, $query->getQuery()->getResult());
    }

}
