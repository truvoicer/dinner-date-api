<?php
namespace App\Controller\Api\User;

use App\Controller\Api\BaseController;
use App\Entity\UserApiToken;
use App\Entity\User;
use App\Service\Tools\HttpRequestService;
use App\Service\Tools\SerializerService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_USER")
 *
 * @Route("/api/user")
 */
class UserController extends BaseController
{
    private UserService $userService;

    /**
     * AdminController constructor.
     * Initialises services used in this controller
     *
     * @param UserService $userService
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(UserService $userService, SerializerService $serializerService,
                                HttpRequestService $httpRequestService)
    {

        parent::__construct($httpRequestService, $serializerService);
        $this->userService = $userService;
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/list", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUserList(Request $request)
    {
        return $this->jsonResponseSuccess(
            "success",
            $this->serializerService->entityToArray(
                $this->userService->findByParams(
                    $this->getUser(),
                    $request->query->all()
                )
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/detail", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUserDetail()
    {
        return $this->jsonResponseSuccess(
            "success",
            $this->serializerService->entityToArray($this->userService->getUserProfile($this->getUser()))
        );
    }

    /**
     * Gets a single api token based on the api token id in the request url
     *
     * @Route("/api-token/{id}/detail", methods={"GET"})
     * @param UserApiToken $userApiToken
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUserUserApiToken(UserApiToken $userApiToken)
    {
        if (!$this->userService->userApiTokenBelongsToUser($this->getUser(), $userApiToken)) {
            return $this->jsonResponseFail(
                "Operation not permitted"
            );
        }
        return $this->jsonResponseSuccess(
            "success",
            $this->serializerService->entityToArray($userApiToken)
        );
    }

    /**
     * Gets a list of usee api tokens based on the user id in the request url
     *
     * @Route("/api-token/list", methods={"GET"})
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUserUserApiTokenList(Request $request)
    {
        $getUserApiTokens = $this->userService->findUserApiTokensByParams(
            $this->getUser(),
            $request->get('sort', "id"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray(
                array_filter($getUserApiTokens, function ($token, $key) {
                    return $token->getType() === "user";
                }, ARRAY_FILTER_USE_BOTH)
            )
        );
    }

    /**
     * Generates a new api token for a single user
     * User is based on the id in the request url
     *
     * @Route("/api-token/generate", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function generateUserApiToken()
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray(
                $this->userService->setUserApiToken($this->getUser())
            )
        );
    }

    /**
     * Delete a single api token based the request post data.
     *
     * @Route("/api-token/delete", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteUserUserApiToken(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $userApiToken = $this->userService->getUserApiTokenById($requestData['item_id']);

        if (!$this->userService->userApiTokenBelongsToUser($this->getUser(), $userApiToken)) {
            return $this->jsonResponseFail(
                "Operation not permitted"
            );
        }
        $delete = $this->userService->deleteUserApiToken($userApiToken);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting api token", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Api Token deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }

    /**
     * Updates a user based on the post request data
     *
     * @param Request $request
     * @Route("/update", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateUser(Request $request)
    {
        $update = $this->userService->updateUser(
            $this->getUser(),
            $this->httpRequestService->getRequestData($request, true));
        if(!$update) {
            return $this->jsonResponseFail("Error updating user");
        }
        return $this->jsonResponseSuccess("User updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/profile/update", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateUserProfile(Request $request)
    {
        $updateUserProfile = $this->userService->updateUserProfile(
            $this->getUser(),
            HttpRequestService::getRequestData($request, true)
        );
        if (!$updateUserProfile) {
            return $this->jsonResponseFail("Error updating user profile", $this->getUser());
        }
        return $this->jsonResponseSuccess(
            "success",
            $this->serializerService->entityToArray(
                $this->getUser(),
                ["full_user"]
            )
        );
    }
}