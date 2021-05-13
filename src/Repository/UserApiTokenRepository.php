<?php

namespace App\Repository;

use App\Entity\UserApiToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UserApiToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserApiToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserApiToken[]    findAll()
 * @method UserApiToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserApiToken::class);
    }

    public function setToken(User $user) {
        try {
            $apiToken = new UserApiToken();
            $apiToken->setToken(bin2hex(random_bytes(60)));
            $apiToken->setExpiresAt( new \DateTime('+1 days'));
            $apiToken->setUser($user);
            $apiToken->setType("auto");

            $this->getEntityManager()->persist($apiToken);
            $this->getEntityManager()->flush();
            return $apiToken;
        } catch (ORMException $e) {
            throw new ORMException("ORM Exception... " . $e->getMessage());
        }
    }

    public function setCustomToken(User $user, string $type, string $token, \DateTime $expiry) {
        try {
            $apiToken = new UserApiToken();
            $apiToken->setToken($token);
            $apiToken->setExpiresAt($expiry);
            $apiToken->setUser($user);
            $apiToken->setType($type);
            $this->getEntityManager()->persist($apiToken);
            $this->getEntityManager()->flush();
            return $apiToken;
        } catch (ORMException $e) {
            throw new ORMException("ORM Exception... " . $e->getMessage());
        }
    }

    public function getLatestToken(User|UserInterface $user) {
        return $this->createQueryBuilder("api_token")
            ->select("api_token")
            ->where("api_token.user = :user")
            ->andWhere("api_token.expiresAt > :currentDate")
            ->setParameter("user", $user)
            ->setParameter("currentDate", new \DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateTokenExpiry(UserApiToken $apiToken, $expiryDate, string $type) {
        try {
            $apiToken->setExpiresAt($expiryDate);
            $apiToken->setType($type);
            $this->getEntityManager()->persist($apiToken);
            $this->getEntityManager()->flush();
            return $apiToken;
        } catch (ORMException $e) {
            throw new ORMException("ORM Exception... " . $e->getMessage());
        }

    }

    public function deleteUserExpiredTokens(User $user) {
        $apiTokens = $this->createQueryBuilder("api_token")
            ->select("api_token")
            ->where("api_token.user = :user")
            ->andWhere("api_token.expiresAt < :currentDate")
            ->andWhere("api_token.type = 'auto'")
            ->setParameter("user", $user)
            ->setParameter("currentDate", new \DateTime())
            ->getQuery()
            ->getResult();
        if (count($apiTokens) === 0) {
            return false;
        }
        foreach ($apiTokens as $token) {
            $this->deleteUserApiToken($token);
        }
        return count($apiTokens);
    }

    public function deleteUserApiToken(UserApiToken $apiToken) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($apiToken);
        $entityManager->flush();
        return $apiToken;
    }
}
