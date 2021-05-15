<?php
namespace App\Security;

use App\Entity\User;
use App\Entity\UserApiToken;
use App\Repository\UserRepository;
use App\Service\Auth\AuthProviderService;
use App\Service\SecurityService;
use App\Service\Tools\HttpRequestService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $em;
    private SecurityService $securityService;
    private UserService $userService;
    private AuthProviderService $externalProviderAuthService;

    public function __construct(
        EntityManagerInterface $em,
        UserService $userService,
        AuthProviderService $externalProviderAuthService,
        SecurityService $securityService
    )
    {
        $this->securityService = $securityService;
        $this->em = $em;
        $this->userService = $userService;
        $this->externalProviderAuthService = $externalProviderAuthService;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): bool
    {
        return $this->securityService->isSupported($request);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $accessToken = $this->securityService->getAccessToken($request);
        if (!$accessToken) {
            throw new CustomUserMessageAuthenticationException("Error retrieving access token.");
        }
        $token = $this->em->getRepository(UserApiToken::class)
            ->findOneBy(['token' => $accessToken]);
        if (!$token) {
            throw new AuthenticationCredentialsNotFoundException("Token not found");
        }
        if ($token->isExpired()) {
            throw new CustomUserMessageAuthenticationException("token_expired");
        }
        return new SelfValidatingPassport(
            new UserBadge(
                $token->getUser()->getEmail(),
                function ($userIdentifier) use($token)  {
                    return $token->getUser();
                }
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