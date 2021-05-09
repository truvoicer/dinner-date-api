<?php

namespace App\Service\Member;

use App\Entity\UserApiToken;
use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserMembership;
use App\Entity\UserProfile;
use App\Repository\PermissionRepository;
use App\Repository\UserApiTokenRepository;
use App\Repository\UserMembershipRepository;
use App\Repository\UserPermissionRepository;
use App\Repository\UserProfileRepository;
use App\Repository\UserRepository;
use App\Service\BaseService;
use App\Service\Tools\HttpRequestService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MemberService extends UserService
{
    protected UserMembershipRepository $userMembershipRepository;
    protected UserProfileRepository $userProfileRepository;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService,
                                TokenStorageInterface $tokenStorage)
    {
        parent::__construct($entityManager, $httpRequestService, $tokenStorage);
        $this->userMembershipRepository = $this->em->getRepository(UserMembership::class);
        $this->userProfileRepository = $this->em->getRepository(UserProfile::class);
    }

    public function getMemberList(User|UserInterface $user, array $params = [])
    {
        return $this->userMembershipRepository->findMembersByMembership(
            "member",
            $params
        );
    }

    public function getMemberProfile(User|UserInterface $user, array $params = [])
    {
        return $this->userProfileRepository->findOneUserProfile(
            $user,
            $params
        );
    }

}