<?php

namespace App\Controller\Api\User\Media;

use App\Controller\Api\BaseController;
use App\Entity\MediaCollection;
use App\Entity\UserApiToken;
use App\Entity\User;
use App\Entity\UserMediaCollection;
use App\Service\Member\MemberService;
use App\Service\Tools\FileSystem\Public\PublicMediaInterface;
use App\Service\Tools\HttpRequestService;
use App\Service\Tools\SerializerService;
use App\Service\User\MediaService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * @Route("/api/user/{user}/media/collection")
 */
class MediaCollectionController extends BaseController
{
    private MediaService $mediaService;
    private PublicMediaInterface $publicMediaInterface;

    /**
     * AdminController constructor.
     * Initialises services used in this controller
     *
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(
        MediaService $mediaService,
        SerializerService $serializerService,
        HttpRequestService $httpRequestService,
        PublicMediaInterface $publicMediaInterface
    )
    {
        parent::__construct($httpRequestService, $serializerService);
        $this->mediaService = $mediaService;
        $this->publicMediaInterface = $publicMediaInterface;
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/{name}/list", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function fetchUserMediaCollections(MediaCollection $mediaCollection)
    {
        return $this->jsonResponseSuccess(
            "Media fetch.",
            $this->serializerService->entityToArray(
                $this->mediaService->getUserMediaCollectionsByCollection($this->getUser(), $mediaCollection),
                ["full_media"]
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/{name}/file/list", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function fetchUserMediaCollectionFiles(User $user, UserMediaCollection $userMediaCollection)
    {
        if ($this->getUser() !== $user) {
            return $this->jsonResponseFail("Action not allowed.");
        }
        return $this->jsonResponseSuccess(
            "Media fetch.",
            $this->serializerService->entityToArray($userMediaCollection, ["full_media"])
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/create", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createUserMediaCollections(Request $request)
    {
        $validate = HttpRequestService::validateRequestData("post", $request, ["name", "collection_name"]);
        if ($validate instanceof JsonResponse) {
            return $validate;
        }
        if (!$this->mediaService->createUserMediaCollection($this->getUser(), $validate)) {
            return $this->jsonResponseFail(
                "Error creating media collection.",
                []
            );
        }
        return $this->jsonResponseSuccess(
            "Created media collection",
            $this->serializerService->entityToArray(
                $this->mediaService->getUserMediaCollectionsByCollectionName($this->getUser(), $validate["collection_name"]),
                ["full_media"]
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/{userMediaCollection}", methods={"PUT"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateUserMediaCollections(UserMediaCollection $userMediaCollection, Request $request)
    {
        return $this->jsonResponseSuccess(
            "Updated media collection.",
            $this->serializerService->entityToArray(
                $this->mediaService->updateUserMediaCollection(
                    $userMediaCollection,
                    $this->getUser(),
                    HttpRequestService::getRequestData($request, true)
                ),
                ["full_media"]
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/{userMediaCollection}", methods={"DELETE"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteUserMediaCollections(UserMediaCollection $userMediaCollection, Request $request)
    {
        if (!$this->mediaService->deleteUserMediaCollection($userMediaCollection)) {
            return $this->jsonResponseFail(
                "Error deleting media collection.",
                []
            );
        }
        return $this->jsonResponseSuccess(
            "Deleted media collection.",
            []
        );
    }
}