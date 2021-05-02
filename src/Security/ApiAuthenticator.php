<?php
namespace App\Security;

use App\Service\SecurityService;
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

class ApiAuthenticator extends AbstractAuthenticator
{
    private SecurityService $securityService;
    private UserPasswordEncoderInterface $passwordEncoder;
    private UserService $userService;

    public function __construct(SecurityService $securityService, UserPasswordEncoderInterface $passwordEncoder,
                                UserService $userService)
    {
        $this->securityService = $securityService;
        $this->passwordEncoder = $passwordEncoder;
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
        $credentials = $this->securityService->getCredentials($request);

        if (null === $credentials) {
            throw new CustomUserMessageAuthenticationException($credentials['email'] . ': Email could not be found.');
        }

        $user = $this->userService->getUserByEmail($credentials["email"]);
        if ($user === null) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException($credentials['email'] . ': Email could not be found.');
        }

//        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
//            throw new CustomUserMessageAuthenticationException('No API token provided');
//        }

        return new Passport(new UserBadge($credentials["email"]), new PasswordCredentials($credentials['password']));
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