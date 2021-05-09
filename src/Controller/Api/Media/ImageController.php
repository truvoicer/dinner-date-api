<?php

namespace App\Controller\Api\Media;

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
 * @Route("/api/media/image")
 */
class ImageController extends BaseController
{
    private MemberService $memberService;
    private PublicMediaInterface $publicMediaInterface;

    /**
     * AdminController constructor.
     * Initialises services used in this controller
     *
     * @param UserService $userService
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
     * @Route("/upload", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadProfileImage(Request $request)
    {
        $this->publicMediaInterface->setUser($this->getUser());
        $upload = $this->publicMediaInterface->mediaUploadHandler();
        if(!$upload) {
            return $this->jsonResponseFail(
                "Error uploading profile picture, try again."
            );
        }
        return $this->jsonResponseSuccess(
            "Successfully uploaded profile picture.",
            $this->serializerService->entityToArray(
                $this->getUser(),
                ["full_user"]
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/delete", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteProfileImage()
    {
        $this->publicMediaInterface->setUser($this->getUser());
        $delete = $this->publicMediaInterface->mediaDeleteHandler();
        if(!$delete) {
            return $this->jsonResponseFail(
                "Error deleting profile picture, try again."
            );
        }
        return $this->jsonResponseSuccess(
            "Successfully deleted profile picture."
        );
    }

}