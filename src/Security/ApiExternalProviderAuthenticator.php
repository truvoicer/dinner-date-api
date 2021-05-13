<?php
namespace App\Security;

use App\Entity\User;
use App\Service\Auth\ExternalProviderAuthService;
use App\Service\SecurityService;
use App\Service\Tools\HttpRequestService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiExternalProviderAuthenticator extends AbstractAuthenticator
{
    private SecurityService $securityService;
    private ExternalProviderAuthService $externalProviderAuthService;
    private UserService $userService;

    public function __construct(
        SecurityService $securityService,
        ExternalProviderAuthService $externalProviderAuthService,
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
        $execute = $this->externalProviderAuthService->validate($requestData["provider"]);
        if (!$execute) {
            throw new CustomUserMessageAuthenticationException('There was an error authenticating the google account.');
        }

        $findUser = $this->userService->getUserByEmail($execute["email"]);
        if ($findUser instanceof User) {
            return new SelfValidatingPassport(
                new UserBadge(
                    $findUser->getEmail()
                )
            );
        }

        $createUser = $this->userService->createUser([
            "username" => $execute["email"],
            "email" => $execute["email"],
            "password" => $requestData["access_token"],
            "confirm_password" => $requestData["access_token"],
            "roles" => ["ROLE_USER"]
        ]);
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