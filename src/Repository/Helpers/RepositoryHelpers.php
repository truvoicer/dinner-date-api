<?php
namespace App\Repository\Helpers;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class RepositoryHelpers
{
    public function getEntityManager(EntityManager $entityManager) {
        if (!$entityManager->isOpen()) {
            $entityManager = $entityManager->create(
                $entityManager->getConnection(),
                $entityManager->getConfiguration()
            );
        }
        return $entityManager;
    }


    public static function addQueryBuilderConditions(QueryBuilder $query, array $conditions = []): QueryBuilder
    {
        if (isset($conditions["sort"]) && isset($conditions["order"])) {
            $query->orderBy($conditions["sort"], $conditions["order"]);
        }
        if (isset($conditions["sort"])) {
            $query->orderBy($conditions["sort"], "desc");
        }
        if (isset($conditions["limit"])) {
            $query->setMaxResults($conditions["limit"]);
        }
        if (isset($conditions["offset"])) {
            $query->setMaxResults($conditions["offset"]);
        }
        return $query;
    }
}