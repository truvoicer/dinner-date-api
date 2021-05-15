<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\Auth\AuthProviderService;
use App\Service\SecurityService;
use App\Service\Tools\HttpRequestService;
use App\Service\Tools\SerializerService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Carbon\Carbon;

/**
 * Contains api endpoint functions for user account tasks via email password login
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @Route("/api/auth")
 */
class AuthController extends BaseController
{
    private UserService $userService;

    /**
     * AuthController constructor.
     * Initialise services for this class
     *
     * @param UserService $userService
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(
        UserService $userService,
        SerializerService $serializerService,
        HttpRequestService $httpRequestService
    )
    {
        parent::__construct($httpRequestService, $serializerService);
        $this->userService = $userService;
    }


    /**
     * API user login
     * Returns user api token data
     *
     * @Route("/external/provider", methods={ "POST" })
     * @param Request $request
     * @return Response
     */
    public function externalProviderAuth(Request $request): Response
    {
        $requestData = HttpRequestService::getRequestData($request, true);
        $apiToken = $this->userService->getLatestToken($this->getUser());
        if ($apiToken === null) {
            return $this->jsonResponseFail("Token not found or expired");
        }
        $this->userService->deleteUserExpiredTokens($this->getUser());
        return $this->jsonResponseSuccess('Token is valid.',
            [
                AuthProviderService::AUTH_USER => $this->serializerService->entityToArray($this->getUser(), ["single"]),
                AuthProviderService::AUTH_TOKEN_PROVIDER => $requestData["provider"],
                AuthProviderService::AUTH_ACCESS_TOKEN => $apiToken->getToken(),
                AuthProviderService::AUTH_EXPIRES_AT => $apiToken->getExpiresAt()->getTimestamp(),
            ]
        );
    }

    /**
     * API user login
     * Returns user api token data
     *
     * @Route("/login", methods={ "POST" })
     * @param Request $request
     * @return Response
     */
    public function authLogin(Request $request): Response
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $user = $this->userService->getUserByEmail($requestData["email"]);
        $apiToken = $this->userService->getLatestToken($user);
        if ($apiToken === null) {
            $apiToken = $this->userService->setUserApiToken($user, UserService::LOCAL_API_TYPE);
        }
        $this->userService->deleteUserExpiredTokens($user);
        return $this->jsonResponseSuccess('Successfully logged in.', [
            AuthProviderService::AUTH_USER => $this->serializerService->entityToArray($user, ["single"]),
            AuthProviderService::AUTH_TOKEN_PROVIDER => "api",
            AuthProviderService::AUTH_ACCESS_TOKEN => $apiToken->getToken(),
            AuthProviderService::AUTH_EXPIRES_AT => $apiToken->getExpiresAt()->getTimestamp(),
        ]);
    }

    /**
     * API user login
     * Returns user api token data
     *
     * @Route("/token/validate", methods={ "GET" })
     * @param Request $request
     * @return Response
     */
    public function authTokenValidate(Request $request): Response
    {
        $apiToken = $this->userService->getLatestToken($this->getUser());
        if ($apiToken === null) {
            return $this->jsonResponseFail("Token not found or expired");
        }
        $this->userService->deleteUserExpiredTokens($this->getUser());
        return $this->jsonResponseSuccess('Token is valid.',
            [
                AuthProviderService::AUTH_USER => $this->serializerService->entityToArray($this->getUser(), ["single"]),
                AuthProviderService::AUTH_TOKEN_PROVIDER => SecurityService::getTokenProviderFromHeader($request),
                AuthProviderService::AUTH_ACCESS_TOKEN => $apiToken->getToken(),
                AuthProviderService::AUTH_EXPIRES_AT => $apiToken->getExpiresAt()->getTimestamp(),
            ]
        );
    }

    /**
     * Generates a new token for a user
     *
     * @Route("/new-token", name="new_token", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newToken(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $user = $this->userService->getUserByEmail($requestData["email"]);
        $setApiToken = $this->userService->setUserApiToken($user, UserService::LOCAL_API_TYPE);
        if (!$setApiToken) {
            return $this->jsonResponseFail("Error generating api token");
        }
        return $this->jsonResponseSuccess("Api token", [
            AuthProviderService::AUTH_USER => $this->serializerService->entityToArray($user, ["single"]),
            AuthProviderService::AUTH_TOKEN_PROVIDER => "api",
            AuthProviderService::AUTH_ACCESS_TOKEN => $setApiToken->getToken(),
            AuthProviderService::AUTH_EXPIRES_AT => $setApiToken->getExpiresAt()->getTimestamp()
        ]);
    }

    /**
     * Generates a new token for a user
     *
     * @Route("/user/create", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createUser(Request $request)
    {
        $user = $this->userService->createUser(
            $this->httpRequestService->getRequestData($request, true)
        );
        if (!$user instanceof User) {
            return $this->jsonResponseFail("User create error", []);
        }
        $setApiToken = $this->userService->setUserApiToken($user, UserService::LOCAL_API_TYPE);
        if (!$setApiToken) {
            return $this->jsonResponseFail("Error generating api token");
        }
        return $this->jsonResponseSuccess('Token is valid.', [
            AuthProviderService::AUTH_USER => $this->serializerService->entityToArray($user, ["single"]),
            AuthProviderService::AUTH_TOKEN_PROVIDER => "api",
            AuthProviderService::AUTH_ACCESS_TOKEN => $setApiToken->getToken(),
            AuthProviderService::AUTH_EXPIRES_AT => $setApiToken->getExpiresAt()->getTimestamp()
        ]);
    }
}
