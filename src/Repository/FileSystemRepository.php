<?php

namespace App\Repository;

use App\Entity\FileSystem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileSystem|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileSystem|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileSystem[]    findAll()
 * @method FileSystem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileSystemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileSystem::class);
    }

    public function findByParams(string $sort, string  $order, int $count)
    {
        $query = $this->createQueryBuilder('fs')
            ->addOrderBy('fs.'.$sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        return $query->getQuery()
            ->getResult()
            ;
    }

    public function getFileSystemObject(FileSystem $fileSystem, ?string $name = null, ?string $basePath = null, ?string $baseUrl = null) {
        if (isset($name)) {
            $fileSystem->setName($name);
        }
        if (isset($basePath)) {
            $fileSystem->setBasePath($basePath);
        }
        if (isset($baseUrl)) {
            $fileSystem->setBaseUrl($baseUrl);
        }
        return $fileSystem;
    }

    public function saveFileSystem(FileSystem $fileSystem)
    {
        $this->getEntityManager()->persist($fileSystem);
        $this->getEntityManager()->flush();
        return $fileSystem;
    }

    public function deleteFileSystem(FileSystem $fileSystem) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($fileSystem);
        $entityManager->flush();
        return true;
    }
}
