<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserAuth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    private $entityManager;
    public const LOGIN_ROUTE = 'account_login';
    public const SECRETKEY_ROUTE = 'account_secret';

    private $urlGenerator;

    private $router;
    private string $returnUrl;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && $this->getLoginUrl($request) === $request->getRequestUri();
    }

    public function authenticate(Request $request): Passport
    {
        // L'email de l'utilisateur
        $email = $request->get('login_form')['email'];

        // Token CSRF
        $token = $request->get('login_form')['_csrf_token'];

        // Le mot de passe crypter
        $password = strtolower(trim($request->get('login_form')['password']));

        $request->getSession()->set(Security::LAST_USERNAME, $email);
        // Gère l'authentification de l'utilisateur en fonction des credentials fournis.
        // Si le user n'est pas trouvé, il est rédirigé de nouveau sur la page de connexion.
        return new Passport(
            new UserBadge($email, function ($email) {
                $findUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if (empty($findUser)) {
                    throw new UserNotFoundException();
                }
                return $findUser;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $token),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if (!empty($this->returnUrl)) {
            return new RedirectResponse($this->returnUrl);
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        // Vérifier si c'est une première connexion
        if ($this->checkUserAuth($token)) {
            return new RedirectResponse($this->router->generate(self::SECRETKEY_ROUTE));
        }
        return new RedirectResponse($this->router->generate('member_dashboard'));

    }

    protected function checkUserAuth(TokenInterface $token): bool
    {
        $user = $token->getUser();
        $checkAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
            'user' => $user,
        ]);
        if (empty($checkAuth)) {
            $newAuth = new UserAuth();
            $newAuth->setIsFirstConnection(false);
            $newAuth->setUser($token->getUser());
            $newAuth->setLastConnectionDate(new \DateTime('now'));
            $this->entityManager->persist($newAuth);
            $this->entityManager->flush();

            return true;
        }

        return $checkAuth->getIsFirstConnection();
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // $this->flashBag->add('loginError', $exception->getMessage());

        return new RedirectResponse($this->router->generate(self::LOGIN_ROUTE));
    }
}
