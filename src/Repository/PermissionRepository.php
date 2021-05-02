<?php

namespace App\Repository;

use App\Entity\Permission;
use App\Service\Tools\UtilsService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function findByParams(string $sort, string  $order, int $count)
    {
        $query = $this->createQueryBuilder('p')
            ->addOrderBy('p.'.$sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        return $query->getQuery()
            ->getResult()
            ;
    }

    public function savePermission(Permission $permission) {
        $this->getEntityManager()->persist($permission);
        $this->getEntityManager()->flush();
        return $permission;
    }


    public function buildPermissionObject(Permission $permission, ?string $name) {
        if (isset($name)) {
            $permission->setName(UtilsService::labelToName($name));
            $permission->setLabel($name);
            return $permission;
        }
        throw new BadRequestHttpException("Permission name not set in request.");
    }

    public function createPermission(string $name) {
        return $this->savePermission(
            $this->buildPermissionObject(new Permission(), $name)
        );
    }


    public function delete(Permission $permission) {
        if ($permission != null) {
            $this->getEntityManager()->remove($permission);
            $this->getEntityManager()->flush();
            return true;
        }
        return false;
    }
}
