<?php

namespace App\Repository;

use App\Entity\MediaCollection;
use App\Entity\User;
use App\Entity\UserMediaCollection;
use App\Repository\Helpers\RepositoryHelpers;
use App\Service\Tools\UtilsService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

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
        array $data
    )
    {
        foreach ($data as $key => $value) {
            $setMethodName = sprintf("set%s", UtilsService::stringToCamelCase($key, true));
            if (method_exists($userMediaCollection, $setMethodName)) {
                if ($key === "name") {
                    $value = UtilsService::stringToSnakeCase($value);
                }
                if ($key === "files") {
                    $this->addFilesToMediaCollection($userMediaCollection, $value);
                    continue;
                }
                if ($key === "media_collection") {
                    $value = $this->getEntityManager()->getRepository(UserMediaCollection::class)->find($value);
                    if ($value === null) {
                        throw new BadRequestHttpException("Media collection [$value] not found.");
                    }
                }
                $userMediaCollection->$setMethodName($value);
            }
        }
        if (isset($data["collection_name"])) {
            $value = $this->getEntityManager()->getRepository(MediaCollection::class)->findOneBy(["name" => $data["collection_name"]]);
            if ($value === null) {
                throw new BadRequestHttpException(sprintf("Media collection [%s] not found.", $data["collection_name"]));
            }
            $userMediaCollection->setMediaCollection($value);
        }

        if (isset($data["name"])) {
            $userMediaCollection->setLabel($data["name"]);
        }
        $userMediaCollection->setUser($user);
        return $userMediaCollection;
    }

    public function addFilesToMediaCollection(UserMediaCollection $userMediaCollection, array $files = [])
    {
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

    public function deleteUserMediaCollectionById(int $id)
    {
        $userMediaCollection = $this->find($id);
        if ($userMediaCollection === null) {
            return false;
        }
        return $this->deleteUserMediaCollection($userMediaCollection);
    }

    public function deleteUserMediaCollection(UserMediaCollection $userMediaCollection)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($userMediaCollection);
        $entityManager->flush();
        return true;
    }

    public function getUserMediaCollectionsByCollection(User|UserInterface $user, string $collection, array $conditions = [])
    {
        $query = $this->createQueryBuilder("umc")
            ->leftJoin("umc.media_collection", "media_collection")
            ->where("umc.user = :user")
            ->andWhere("media_collection.name = :collection")
            ->setParameter("user", $user)
            ->setParameter("collection", $collection);
        return RepositoryHelpers::addQueryBuilderConditions($query, $conditions)
            ->getQuery()
            ->getResult();
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
