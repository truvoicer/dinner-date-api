<?php

namespace App\Controller\Api;

use App\Service\SecurityService;
use App\Service\Tools\HttpRequestService;
use App\Service\Tools\SerializerService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
            $apiToken = $this->userService->setUserApiToken($user, "auto");
        }
        $this->userService->deleteUserExpiredTokens($user);
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => 'Successfully logged in.',
            'session' => [
                "access_token" => $apiToken->getToken(),
                "expires_at" => $apiToken->getExpiresAt()->getTimestamp()
            ],
        ];

        return $this->jsonResponseSuccess("success", $data);
    }

    /**
     * API user login
     * Returns user api token data
     *
     * @Route("/token/validate", methods={ "POST" })
     * @param Request $request
     * @return Response
     */
    public function authTokenValidate(): Response
    {
        $apiToken = $this->userService->getLatestToken($this->getUser());
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => 'Successfully logged in.',
            'session' => [
                "email" => $this->getUser()->getEmail(),
                "access_token" => $apiToken->getToken(),
                "expires_at" => $apiToken->getExpiresAt()->getTimestamp()
            ],
        ];
        return $this->jsonResponseSuccess("success", $data);
    }

    /**
     * Gets user data
     *
     * @Route("/user/details", methods={ "POST" })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAuthUserDetails()
    {
        return $this->jsonResponseSuccess("Success",
            $this->serializerService->entityToArray($this->getUser()));
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
        $setApiToken = $this->userService->setUserApiToken($user, "auto");
        if (!$setApiToken) {
            return $this->jsonResponseFail("Error generating api token");
        }
        return $this->jsonResponseSuccess("Api token", [
            "token: " => $setApiToken->getToken(),
            "expiresAt" => $setApiToken->getExpiresAt()->format("Y-m-d H:i:s"),
            "email" => $setApiToken->getuser()->getEmail()
        ]);
    }
}
