<?php

namespace App\Repository;

use App\Entity\FileDownload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileDownload|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileDownload|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileDownload[]    findAll()
 * @method FileDownload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileDownloadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileDownload::class);
    }

    public function findByParams(string $sort, string  $order, int $count)
    {
        $query = $this->createQueryBuilder('fd')
            ->addOrderBy('fd.'.$sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        return $query->getQuery()
            ->getResult()
            ;
    }

    public function findByQuery($query)
    {
        return $this->createQueryBuilder('fd')
            ->where("fd.download_key LIKE :query")
            ->setParameter("query", "%" . $query . "%")
            ->getQuery()
            ->getResult();
    }

    public function saveFileDownload(FileDownload $fileDownload)
    {
        $this->getEntityManager()->persist($fileDownload);
        $this->getEntityManager()->flush();
        return $fileDownload;
    }

    public function deleteFileDownload(FileDownload $fileDownload) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($fileDownload);
        $entityManager->flush();
        return $fileDownload;
    }
}
