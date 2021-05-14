<?php
namespace App\Service\Auth;

use App\Entity\User;
use App\Service\BaseService;
use App\Service\ServiceFactory;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthProviderService extends BaseService
{
    private ServiceFactory $serviceFactory;

    public function __construct(ServiceFactory $serviceFactory) {
        $this->serviceFactory = $serviceFactory;
    }

    public function validatePostRequest(string $provider) {
        return match ($provider) {
            "google" => $this->serviceFactory->getService("auth.service.google")->validatePostRequest(),
            default => false,
        };
    }

    public function validateTokenRequest(string $provider) {
        return match ($provider) {
            "google" => $this->serviceFactory->getService("auth.service.google")->validateTokenRequest(),
            default => false,
        };
    }

    public function updateUserProfile(string $provider, User|UserInterface $user) {
        return match ($provider) {
            "google" => $this->serviceFactory->getService("auth.service.google")->updateUserProfile($user),
            default => false,
        };
    }
}