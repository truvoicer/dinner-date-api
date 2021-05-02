<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    protected UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getUserObject(User $user, array $data, ?string $password = null)
    {
        if (isset($data['display_name'])) {
            $user->setDisplayName($data['display_name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        } else {
            throw new BadRequestHttpException("Email must be set");
        }

        if (isset($data['roles'])) {
            $roles = $data["roles"];
            if (!is_array($roles)) {
                $roles = json_decode($data['roles'], true);
            }
            $user->setRoles($roles);
        }
        return $user;
    }

    public function setUserPassword(User $user, array $data, $type)
    {
        if ((array_key_exists("change_password", $data) && $data["change_password"]) || $type === "insert") {
            if (!array_key_exists("confirm_password", $data) || !array_key_exists("new_password", $data)) {
                throw new BadRequestHttpException("confirm_password or new_password is not in request.");
            }
            if ($data["confirm_password"] === "" || $data["confirm_password"] === null ||
                $data["new_password"] === "" || $data["new_password"] === null) {
                throw new BadRequestHttpException("Confirm or New Password fields have empty values.");
            }
            if ($data["confirm_password"] !== $data["new_password"]) {
                throw new BadRequestHttpException("Confirm and New Password fields don't match.");
            }
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $data['new_password'])
            );
            return $user;
        }
        return $user;
    }

    public function createUser(User $user)
    {
        $user->setDateUpdated(new DateTime());
        $user->setDateCreated(new DateTime());
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }

    public function updateUser(User $user)
    {
        $user->setDateUpdated(new DateTime());
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }

    public function deleteUser(User $user) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($user);
        $entityManager->flush();
        return $user;
    }

    public function findByParams(string $sort,  string $order, int $count)
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

    public function findApiTokensByParams(User $user, string $sort,  string $order, int $count)
    {
        $em = $this->getEntityManager();
        return $em->createQuery("SELECT apitok FROM App\Entity\UserApiToken apitok
                                   WHERE apitok.user = :user")
            ->setParameter('user', $user)
            ->getResult();
    }

    public function findByEmail(string $email)
    {
        return $this->findOneBy(["email" => $email]);
    }
}
