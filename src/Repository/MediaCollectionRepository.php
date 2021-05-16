<?php

namespace App\Repository;

use App\Entity\MediaCollection;
use App\Repository\Helpers\RepositoryHelpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MediaCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaCollection[]    findAll()
 * @method MediaCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaCollection::class);
    }

    public function getMediaCollectionObject(MediaCollection $mediaCollection, ?string $name, ?string $label)
    {
        if (isset($name)) {
            $mediaCollection->setName($name);
        }
        if (isset($label)) {
            $mediaCollection->setDisplayname($label);
        }
        return $mediaCollection;
    }

    public function createMediaCollection(MediaCollection $mediaCollection)
    {
        $this->getEntityManager()->persist($mediaCollection);
        $this->getEntityManager()->flush();
        return $mediaCollection;
    }

    public function updateMediaCollection(MediaCollection $mediaCollection)
    {
        $this->getEntityManager()->persist($mediaCollection);
        $this->getEntityManager()->flush();
        return $mediaCollection;
    }

    public function deleteMediaCollection(MediaCollection $mediaCollection)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($mediaCollection);
        $entityManager->flush();
        return $mediaCollection;
    }

    public function findByParams(array $conditions = [])
    {
        return RepositoryHelpers::addQueryBuilderConditions(
            $this->createQueryBuilder("media_collection"),
            $conditions
        )
            ->getQuery()
            ->getResult();
    }
}
