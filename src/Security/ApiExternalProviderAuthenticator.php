<?php
namespace App\Security;

use App\Entity\User;
use App\Service\Auth\AuthProviderService;
use App\Service\SecurityService;
use App\Service\Tools\HttpRequestService;
use App\Service\Tools\UtilsService;
use App\Service\User\UserService;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiExternalProviderAuthenticator extends AbstractAuthenticator
{
    private SecurityService $securityService;
    private AuthProviderService $externalProviderAuthService;
    private UserService $userService;

    public function __construct(
        SecurityService $securityService,
        AuthProviderService $externalProviderAuthService,
        UserService $userService
    )
    {
        $this->securityService = $securityService;
        $this->externalProviderAuthService = $externalProviderAuthService;
        $this->userService = $userService;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request) :bool
    {
        return $this->securityService->isSupported($request);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $requestData = HttpRequestService::getRequestData($request, true);
        $execute = $this->externalProviderAuthService->validatePostRequest($requestData["provider"]);
        if (!$execute) {
            throw new CustomUserMessageAuthenticationException('There was an error authenticating the google account.');
        }

        $createUser = $this->userService->getUserByEmail($execute["email"]);

        if ($createUser === null) {
            $password = UtilsService::randomStringGenerator(24);
            $createUser = $this->userService->createUser([
                "username" => $execute[AuthProviderService::AUTH_EMAIL],
                "email" => $execute[AuthProviderService::AUTH_EMAIL],
                "password" => $password,
                "confirm_password" => $password,
                "roles" => ["ROLE_USER"]
            ]);
            $this->userService->updateUserProfile($createUser, [
                "first_name" => $execute[AuthProviderService::AUTH_FIRST_NAME],
                "last_name" => $execute[AuthProviderService::AUTH_LAST_NAME]
            ]);
        }

        $setApiToken = $this->userService->setUserApiToken(
            $createUser,
            $execute[AuthProviderService::AUTH_TOKEN_PROVIDER]
        );
        if (!$setApiToken) {
            throw new CustomUserMessageAuthenticationException('Error saving api token.');
        }
        return new SelfValidatingPassport(
            new UserBadge(
                $createUser->getEmail()
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}