<?php

namespace App\Security;

use App\Entity\UtilisateurSimple;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    public const LOGIN_ROUTE_RESUME = 'app_auth_resume';

    public const DEFAULT_ROUTE = 'app_default';

    public const DEFAULT_ROUTE_USER = 'new_site';
    public const DEFAULT_ROUTE_RESUME = 'resume';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }


    public function supports(Request $request): bool
    {

        /* dd(); */

        if ($request->attributes->get('_route') == self::LOGIN_ROUTE) {
            return self::LOGIN_ROUTE === $request->attributes->get('_route')
                && $request->isMethod('POST');
        } else {
            // dd('');
            return self::LOGIN_ROUTE_RESUME === $request->attributes->get('_route')
                && $request->isMethod('POST');
        }
        //return $response;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {


        //dd($token->getUser(), $request->attributes->get('_route'));
        $route = '';
        if (($token->getUser() instanceof UtilisateurSimple) && ($request->attributes->get('_route') == self::LOGIN_ROUTE_RESUME)) {
            //dd("1");
            $route = self::DEFAULT_ROUTE_RESUME;
        } elseif (($token->getUser() instanceof UtilisateurSimple) && ($request->attributes->get('_route') != self::LOGIN_ROUTE_RESUME)) {
            //dd("2");
            $route = self::DEFAULT_ROUTE_USER;
        } else {
            //dd("3");
            $route = self::DEFAULT_ROUTE;
        }


        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }


        return new RedirectResponse($this->urlGenerator->generate($route));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
