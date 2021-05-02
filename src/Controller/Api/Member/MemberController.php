<?php
namespace App\Controller\Api\Member;

use App\Controller\Api\BaseController;
use App\Entity\UserApiToken;
use App\Entity\User;
use App\Service\Member\MemberService;
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
 * @Route("/api/member")
 */
class MemberController extends BaseController
{
    private MemberService $memberService;

    /**
     * AdminController constructor.
     * Initialises services used in this controller
     *
     * @param UserService $userService
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(MemberService $memberService, SerializerService $serializerService,
                                HttpRequestService $httpRequestService)
    {

        parent::__construct($httpRequestService, $serializerService);
        $this->memberService = $memberService;
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/list", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMemberList(Request $request)
    {
        return $this->jsonResponseSuccess(
            "success",
            $this->serializerService->entityToArray(
                $this->memberService->getMemberList(
                    $this->getUser(),
                    $request->query->all()
                ),
                ["members_list"]
            )
        );
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/detail", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMemberProfile()
    {
        return $this->jsonResponseSuccess(
            "success",
            $this->serializerService->entityToArray(
                $this->memberService->getMemberList(
                    $this->getUser(),
                    $request->query->all()
                )
            )
        );
    }

}