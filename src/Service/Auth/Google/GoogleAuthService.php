<?php

namespace App\Service\Auth\Google;

use App\Service\Auth\AuthProviderService;
use App\Service\BaseService;
use App\Service\Tools\HttpRequestService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function validatePostRequest()
    {
        $ticket = $this->googleClient->verifyIdToken($this->requestData["id_token"]);
        if (!is_array($ticket)) {
            return false;
        }
        return [
            AuthProviderService::AUTH_TOKEN_PROVIDER => self::AUTH_SERVICE_NAME,
            AuthProviderService::AUTH_ACCESS_TOKEN => $this->requestData["id_token"],
            AuthProviderService::AUTH_EXPIRES_AT => $ticket["exp"],
            AuthProviderService::AUTH_EMAIL => $ticket["email"],
        ];
    }
}