<?php

namespace App\Service\Auth\Facebook;

use App\Entity\User;
use App\Service\Auth\AuthProviderService;
use App\Service\BaseService;
use App\Service\Tools\HttpRequestService;
use App\Service\Tools\UtilsService;
use App\Service\User\UserService;
use Exception;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class FacebookAuthService extends BaseService
{
    const AUTH_SERVICE_NAME = "facebook";

    private \League\OAuth2\Client\Provider\Facebook $facebookClient;
    private ?Request $request;
    private array $requestData;
    private UserService $userService;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        UserService $userService,
        RequestStack $request,
        ParameterBagInterface $parameterBag
    )
    {
        $this->request = $request->getCurrentRequest();
        $this->requestData = HttpRequestService::getRequestData($this->request, true);
        $this->userService = $userService;
        $this->parameterBag = $parameterBag;
        $this->setClient();
    }

    private function setClient()
    {
        $this->facebookClient = new \League\OAuth2\Client\Provider\Facebook([
            'clientId' => $this->parameterBag->get("app.facebook.app_id"),
            'clientSecret' => $this->parameterBag->get("app.facebook.app_secret"),
            'graphApiVersion' => $this->parameterBag->get("app.facebook.graph_version"),
            'redirectUri' => $this->parameterBag->get("app.facebook.redirect_uri"),
        ]);
    }

    public function validatePostRequest()
    {
        try {
            $accessToken = $this->facebookClient->getAccessToken('fb_exchange_token', [
                'fb_exchange_token' => $this->requestData["accessToken"]
            ]);
            $getFbUser = $this->facebookClient->getResourceOwner($accessToken);

            if (!$getFbUser->getEmail()) {
                throw new BadRequestHttpException("Error pulling email address from facebook.");
            }

            return [
                AuthProviderService::AUTH_TOKEN_PROVIDER => self::AUTH_SERVICE_NAME,
                AuthProviderService::AUTH_ACCESS_TOKEN => $this->requestData["accessToken"],
                AuthProviderService::AUTH_EXPIRES_AT => $accessToken->getExpires(),
                AuthProviderService::AUTH_EMAIL => $getFbUser->getEmail(),
                AuthProviderService::AUTH_FIRST_NAME => $getFbUser->getFirstName(),
                AuthProviderService::AUTH_LAST_NAME =>$getFbUser->getLastName(),
                AuthProviderService::AUTH_PROFILE_PIC_URL => $getFbUser->getPictureUrl(),
            ];
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}