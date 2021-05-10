<?php

namespace App\Controller\Api\Locale;

use App\Controller\Api\BaseController;
use App\Entity\UserApiToken;
use App\Entity\User;
use App\Service\Locale\LocaleService;
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
 * @Route("/api/locale")
 */
class LocaleController extends BaseController
{
    private LocaleService $localeService;

    /**
     * AdminController constructor.
     * Initialises services used in this controller
     *
     */
    public function __construct(
        LocaleService $localeService,
        SerializerService $serializerService,
        HttpRequestService $httpRequestService
    )
    {
        parent::__construct($httpRequestService, $serializerService);
        $this->localeService = $localeService;
    }

    /**
     * Gets a single user based on the id in the request url
     *
     * @Route("/country/list", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCountryList(Request $request)
    {
        return $this->jsonResponseSuccess(
            "Country list fetch",
            $this->serializerService->entityToArray(
                $this->localeService->getCountryList(
                    $request->query->all()
                ),
                ["list"]
            )
        );
    }

}