<?php

namespace UserBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use UserBundle\Doctrine\UserManager;
use UserBundle\Entity\User;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var string
     */
    private $defaultTargetPath;


    public function __construct(UserManager $userManager, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, string $defaultTargetPath)
    {
        $this->userManager = $userManager;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->defaultTargetPath = $defaultTargetPath;
    }

    public function supports(Request $request): bool
    {
        return 'user_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request): array
    {
        $credentials = [
            'username'   => $request->request->get('_username'),
            'password'   => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->userManager->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('User could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate($this->defaultTargetPath));
    }

    protected function getLoginUrl(): string
    {
        return $this->router->generate('user_login');
    }
}
