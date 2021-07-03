<?php

namespace App\Controller\Api\User\Media;

use App\Controller\Api\BaseController;
use App\Entity\UserApiToken;
use App\Entity\User;
use App\Service\Member\MemberService;
use App\Service\Tools\FileSystem\Public\PublicMediaInterface;
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
 * @Route("/api/user/{user}/media")
 */
class MediaController extends BaseController
{
    private MemberService $memberService;
    private PublicMediaInterface $publicMediaInterface;

    /**
     * AdminController constructor.
     * Initialises services used in this controller
     *
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(
        MemberService $memberService,
        SerializerService $serializerService,
        HttpRequestService $httpRequestService,
        PublicMediaInterface $publicMediaInterface
    )
    {
        parent::__construct($httpRequestService, $serializerService);
        $this->memberService = $memberService;
        $this->publicMediaInterface = $publicMediaInterface;
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/fetch", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function fetchPublicMedia()
    {
        $this->publicMediaInterface->setUser($this->getUser());
        return $this->jsonResponseSuccess(
            "Media fetch.",
            $this->serializerService->entityToArray(
                $this->publicMediaInterface->mediaFetchHandler(),
                ["full_media"]
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/upload", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadPublicMedia(Request $request)
    {
        $requestData = HttpRequestService::getRequestData($request);
        $this->publicMediaInterface->setUser($this->getUser());
        $upload = $this->publicMediaInterface->mediaUploadHandler();
        if(!$upload) {
            return $this->jsonResponseFail(
                "Error uploading profile picture, try again."
            );
        }
        return match ($requestData["upload_type"]) {
            "media" => $this->jsonResponseSuccess(
                "Successfully uploaded media.",
                $this->serializerService->entityToArray(
                    $this->publicMediaInterface->getFileSystemCrudService()->findUserFilesByMediaCategory(
                        $this->getUser(), $requestData["media_category"]
                    ),
                    ["full_media"]
                )
            ),
            default => $this->jsonResponseSuccess(
                "Successfully uploaded profile picture.",
                $this->serializerService->entityToArray(
                    $this->getUser(),
                    ["full_user"]
                )
            ),
        };
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/delete", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deletePublicMedia()
    {
        $this->publicMediaInterface->setUser($this->getUser());
        return $this->jsonResponseSuccess(
            "Successfully deleted profile picture.",
            $this->serializerService->entityArrayToArray(
                $this->publicMediaInterface->mediaDeleteHandler(),
                ["members_list"]
            )
        );
    }

}