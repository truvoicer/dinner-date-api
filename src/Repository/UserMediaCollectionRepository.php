<?php

namespace App\Repository;

use App\Entity\MediaCollection;
use App\Entity\User;
use App\Entity\UserMediaCollection;
use App\Repository\Helpers\RepositoryHelpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMediaCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMediaCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMediaCollection[]    findAll()
 * @method UserMediaCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMediaCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMediaCollection::class);
    }

    public function getUserMediaCollectionObject(
        UserMediaCollection $userMediaCollection,
        User $user,
        MediaCollection $mediaCollection,
        ?string $name,
        ?string $label,
        array $files
    )
    {
        if (isset($name)) {
            $userMediaCollection->setName($name);
        }
        if (isset($label)) {
            $userMediaCollection->setLabel($label);
        }
        $userMediaCollection->setUser($user);
        $userMediaCollection->setMediaCollection($mediaCollection);
        foreach ($files as $file) {
            $userMediaCollection->addFile($file);
        }
        return $userMediaCollection;
    }

    public function createUserMediaCollection(UserMediaCollection $userMediaCollection)
    {
        $this->getEntityManager()->persist($userMediaCollection);
        $this->getEntityManager()->flush();
        return $userMediaCollection;
    }

    public function updateUserMediaCollection(UserMediaCollection $userMediaCollection)
    {
        $this->getEntityManager()->persist($userMediaCollection);
        $this->getEntityManager()->flush();
        return $userMediaCollection;
    }

    public function deleteUserMediaCollection(UserMediaCollection $userMediaCollection)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($userMediaCollection);
        $entityManager->flush();
        return $userMediaCollection;
    }

    public function findByParams(array $conditions = [])
    {
        return RepositoryHelpers::addQueryBuilderConditions(
            $this->createQueryBuilder("umc"),
            $conditions
        )
            ->getQuery()
            ->getResult();
    }
}
