<?php
namespace App\Service\Auth;

use App\Entity\User;
use App\Service\BaseService;
use App\Service\ServiceFactory;
use Symfony\Component\Security\Core\User\UserInterface;

class ExternalProviderAuthService extends BaseService
{
    private ServiceFactory $serviceFactory;

    public function __construct(ServiceFactory $serviceFactory) {
        $this->serviceFactory = $serviceFactory;
    }

    public function validate(string $provider) {
        return match ($provider) {
            "google" => $this->serviceFactory->getService("auth.service.google")->validate(),
            default => false,
        };
    }

    public function updateUserProfile(string $provider, User|UserInterface $user) {
        return match ($provider) {
            "google" => $this->serviceFactory->getService("auth.service.google")->updateUserProfile($user),
            default => false,
        };
    }

    public function getToken(string $provider, User|UserInterface $user) {
        return match ($provider) {
            "google" => $this->serviceFactory->getService("auth.service.google")->getToken($user),
            default => false,
        };
    }

}