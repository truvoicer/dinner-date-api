<?php
namespace App\Service;

use App\Service\Auth\Google\GoogleAuthService;
use App\Service\Tools\HttpRequestService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SecurityService
{
    const SUPPORTED_METHODS = [
        "GET", "POST", "PUT"
    ];

    const SUPPORTED_TOKEN_PROVIDERS = [
        "api",
        GoogleAuthService::AUTH_SERVICE_NAME
    ];

    private $httpRequestService;

    public function __construct(HttpRequestService $httpRequestService)
    {
        $this->httpRequestService = $httpRequestService;
    }

    public function isSupported(Request $request)
    {
        if (!$this->checkSupportedMethods($request->getMethod())) {
            return false;
        }
        return true;
    }

    public function checkSupportedMethods(string $method)
    {
        if (in_array($method, self::SUPPORTED_METHODS)) {
            return true;
        }
        return false;
    }

    public function checkAuthorizationHeader(Request $request)
    {
        if ($request->headers->has("Authorization") &&
        0 == strpos($request->headers->has("Authorization"), "Bearer")) {
            return true;
        }
        return false;
    }

    public function getAccessToken(Request $request) {
        if ($this->checkAuthorizationHeader($request)) {
            return $this->getTokenFromHeader($request->headers->get('Authorization'));
        }
        return false;
    }

    public function getTokenProvider(Request $request) {
        return $this->getTokenFromHeader($request->headers->get('Token-Provider'));
    }

    public function getTokenFromHeader($headerValue) {
        if ($headerValue === null || $headerValue === "") {
            throw new CustomUserMessageAuthenticationException("Empty authorization header.");
        }
        if (!substr( $headerValue, 0, 7 ) === "Bearer ") {
            throw new CustomUserMessageAuthenticationException("Invalid Bearer token.");
        }
        return str_replace("Bearer ", "", $headerValue);
    }

    public function getCredentials(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request, true);

        if (!array_key_exists("email", $requestData) ||
            $requestData["email"] === "" ||
            $requestData["email"] === null
        ) {
            throw new CustomUserMessageAuthenticationException("Invalid email.");
        }
        if (!array_key_exists("password", $requestData) ||
            $requestData["password"] === "" ||
            $requestData["password"] === null
        ) {
            throw new CustomUserMessageAuthenticationException("Invalid password.");
        }
        return [
            'email' => $requestData['email'],
            'password' => $requestData['password'],
        ];
    }
}