<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\User;
use App\Repository\Helpers\RepositoryHelpers;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function findUserFilesByMediaCategory(User|UserInterface $user, array $mediaCategory, array $conditions = [])
    {
        return RepositoryHelpers::addQueryBuilderConditions(
            $this->createQueryBuilder("file"), $conditions
        )
            ->leftJoin("file.user", "user")
            ->where("user = :user")
            ->andWhere("file.media_category in (:mediaCategory)")
            ->setParameter("user", $user)
            ->setParameter("mediaCategory", $mediaCategory)
            ->getQuery()->
            getResult();
    }

    public function findByParams(string $sort, string $order, int $count)
    {
        $query = $this->createQueryBuilder('fs')
            ->addOrderBy('fs.' . $sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        return $query->getQuery()
            ->getResult();
    }

    public function findByQuery($query)
    {
        return $this->createQueryBuilder('fs')
            ->where("fs.filename LIKE :query")
            ->setParameter("query", "%" . $query . "%")
            ->getQuery()
            ->getResult();
    }

    public function saveFile(File $file)
    {
        $file->setDateUpdated(new DateTime());
        $file->setDateCreated(new DateTime());
        $this->getEntityManager()->persist($file);
        $this->getEntityManager()->flush();
        return $file;
    }

    public function deleteFile(File $fileSystemItem)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($fileSystemItem);
        $entityManager->flush();
        return $fileSystemItem;
    }
}
