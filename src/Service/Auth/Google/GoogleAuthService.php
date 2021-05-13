<?php

namespace App\Service\Auth\Google;

use App\Entity\User;
use App\Service\BaseService;
use App\Service\Tools\HttpRequestService;
use App\Service\User\UserService;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class GoogleAuthService extends BaseService
{
    const AUTH_SERVICE_NAME = "google";

    private \Google\Client $googleClient;
    private ?Request $request;
    private array $requestData;
    private UserService $userService;

    public function __construct(
        UserService $userService,
        RequestStack $request
    )
    {
        $this->setGoogleClient();
        $this->request = $request->getCurrentRequest();
        $this->requestData = HttpRequestService::getRequestData($this->request, true);
        $this->userService = $userService;
    }

    private function setGoogleClient()
    {
        $this->googleClient = new \Google\Client();

    }

    public function validate()
    {
        $ticket = $this->googleClient->verifyIdToken($this->requestData["id_token"]);
        if (is_array($ticket)) {
            return $ticket; // user ID
        }
        return false;
    }

    public function updateUserProfile(User|UserInterface $user)
    {
        return $this->userService->updateUserProfile($user, [
            "first_name" => $this->requestData["givenName"],
            "last_name" => $this->requestData["familyName"],
        ]);
    }

    public function getToken()
    {
        return [
            "token_provider" => self::AUTH_SERVICE_NAME,
            "access_token" => $this->requestData["id_token"],
            "expires_at" => round($this->requestData["expires_at"] / 1000)
        ];
    }
}