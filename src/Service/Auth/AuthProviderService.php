<?php
namespace App\Service\Auth;

use App\Service\Auth\Facebook\FacebookAuthService;
use App\Service\Auth\Google\GoogleAuthService;
use App\Service\BaseService;
use App\Service\ServiceFactory;

class AuthProviderService extends BaseService
{
    const AUTH_TOKEN_PROVIDER = "token_provider";
    const AUTH_ACCESS_TOKEN = "access_token";
    const AUTH_EXPIRES_AT = "expires_at";
    const AUTH_EMAIL = "email";
    const AUTH_USER = "user";

    const AUTH_FIRST_NAME = "first_name";
    const AUTH_LAST_NAME = "last_name";
    const AUTH_PROFILE_PIC_URL = "profile_pic_url";

    private ServiceFactory $serviceFactory;

    public function __construct(ServiceFactory $serviceFactory) {
        $this->serviceFactory = $serviceFactory;
    }

    public function validatePostRequest(string $provider) {
        return match ($provider) {
            GoogleAuthService::AUTH_SERVICE_NAME => $this->serviceFactory->getService("auth.service.google")->validatePostRequest(),
            FacebookAuthService::AUTH_SERVICE_NAME => $this->serviceFactory->getService("auth.service.facebook")->validatePostRequest(),
            default => false,
        };
    }
}