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
 * @IsGranted("ROLE_USER")
 * @Route("/api/session")
 */
class SessionController extends BaseController
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
     * Gets user data
     *
     * @Route("/user/detail", methods={ "GET" })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSessionUserDetail()
    {
        return $this->jsonResponseSuccess("User fetch successful",
            $this->serializerService->entityToArray(
                $this->getUser(),
                ["full_user"]
            )
        );
    }

}
