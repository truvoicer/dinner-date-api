<?php

namespace App\Service\User;

use App\Entity\UserApiToken;
use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\PermissionRepository;
use App\Repository\UserApiTokenRepository;
use App\Repository\UserProfileRepository;
use App\Repository\UserRepository;
use App\Service\BaseService;
use App\Service\Tools\HttpRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService extends BaseService
{

    protected EntityManagerInterface $em;
    protected UserRepository $userRepository;
    protected UserProfileRepository $userProfileRepository;
    protected PermissionRepository $permissionRepository;
    protected UserApiTokenRepository$userApiTokenRepository;
    protected HttpRequestService $httpRequestService;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService,
                                TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->userRepository = $this->em->getRepository(User::class);
        $this->userProfileRepository = $this->em->getRepository(UserProfile::class);
        $this->permissionRepository = $this->em->getRepository(Permission::class);
        $this->userApiTokenRepository = $this->em->getRepository(UserApiToken::class);
        $this->httpRequestService = $httpRequestService;

    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function userApiTokenBelongsToUser(User $user, UserApiToken $userApiToken)
    {
        return $userApiToken->getUser()->getId() === $user->getId();
    }

    public function getUserApiTokenById(int $id)
    {
        $userApiToken = $this->userApiTokenRepository->find($id);
        if ($userApiToken === null) {
            throw new BadRequestHttpException(sprintf("Api token id: %s not found in database.",
                $userApiToken
            ));
        }
        return $userApiToken;
    }

    public function setUserApiToken(User $user, string $type)
    {
        $userApiTokenRepository = $this->em->getRepository(UserApiToken::class);
        return $userApiTokenRepository->setToken($user, $type);
    }

    public function updateApiTokenExpiry(UserApiToken $userApiToken, array $data)
    {
        return $this->userApiTokenRepository->updateTokenExpiry($userApiToken, new \DateTime($data["expires_at"]), "user");
    }

    public function getLatestToken(User|UserInterface $user)
    {
        return $this->userApiTokenRepository->getLatestToken($user);
    }

    public function getTokenByValue(string $tokenValue): UserApiToken
    {
        return $this->userApiTokenRepository->findOneBy(["token" => $tokenValue]);
    }

    public function findUserApiTokensByParams(User|UserInterface $user, string $sort, string $order, int $count)
    {
        return $this->userRepository->findUserApiTokenByParams($user, $sort, $order, $count);
    }

    public function findUsers(User|UserInterface $user, array $params = [])
    {
        return $this->userRepository->findByParams($sort, $order, $count);
    }

    public function createUser(array $data)
    {
        $user = $this->userRepository->getUserObject(new User(), $data);
        $user = $this->userRepository->setUserPassword($user, $data, "insert");
        if ($this->httpRequestService->validateData($user)) {
            return $this->userRepository->createUser($user);
        }
        return false;
    }

    public function updateUser(User|UserInterface $user, array $data)
    {
        $getUser = $this->userRepository->setUserPassword(
            $this->userRepository->getUserObject($user, $data),
            $data,
            "update"
        );
        if ($this->httpRequestService->validateData($getUser)) {
            return $this->userRepository->updateUser($getUser);
        }
        return false;
    }

    public function updateUserProfile(User|UserInterface $user, array $data)
    {
        $getUserProfileObject = $this->userProfileRepository->getUserProfileObject($user->getUserProfile(), $data);
        if ($this->httpRequestService->validateData($getUserProfileObject)) {
            return $this->userProfileRepository->updateUserProfile($getUserProfileObject);
        }
        return false;
    }

    public function deleteUserById(int $userId)
    {
        $user = $this->userRepository->findOneBy(["id" => $userId]);
        if ($user === null) {
            throw new BadRequestHttpException(sprintf("User id: %s not found in database.", $userId));
        }
        return $this->deleteUser($user);
    }

    public function deleteUser(User $user)
    {
        return $this->userRepository->deleteUser($user);
    }

    public function deleteUserExpiredTokens(User $user)
    {
        return $this->userApiTokenRepository->deleteUserExpiredTokens($user);
    }

    public function deleteUserApiTokenById(int $id)
    {
        $userApiToken = $this->userApiTokenRepository->findOneBy(["id" => $id]);
        if ($userApiToken === null) {
            throw new BadRequestHttpException("UserApiToken does not exist in database...");
        }
        return $this->deleteUserApiToken($userApiToken);
    }

    public function deleteUserApiToken(UserApiToken $userApiToken)
    {
        return $this->userApiTokenRepository->deleteUserApiToken($userApiToken);
    }
}