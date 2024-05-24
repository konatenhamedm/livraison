<?php

namespace App\Controller;

use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\DTO\InscriptionDTO;

class SecurityController extends AbstractController
{
    #[Route(path: '/sf-login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() && in_array('ROLE_CLIENT', $this->getUser()->getRoles())) {


            return $this->redirectToRoute('new_site');
        }

        if ($this->getUser() && !in_array('ROLE_CLIENT', $this->getUser()->getRoles())) {


            return $this->redirectToRoute('app_default');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route(path: '/authentication/resume', name: 'app_auth_resume')]
    public function loginResume(AuthenticationUtils $authenticationUtils): Response
    {


        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        $inscriptionDTO = new InscriptionDTO();
        $form = $this->createForm(RegisterType::class, $inscriptionDTO, [
            'method' => 'POST',
            //'type'=>'autre',
            'action' => $this->generateUrl('app_register'),
        ]);

        return $this->render('new_site/authentification.html.twig', ['last_username' => $lastUsername, 'error' => $error,  'form' => $form->createView()]);
    }
    #[Route(path: '/authentication/simple', name: 'app_auth_simple')]
    public function loginSimple(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $inscriptionDTO = new InscriptionDTO();
        $form = $this->createForm(RegisterType::class, $inscriptionDTO, [
            'method' => 'POST',
            //'type'=>'autre',
            'action' => $this->generateUrl('app_register'),
        ]);

        $redirect = $request->query->get('redirect', '');


        return $this->render('new_site/authentification_simple.html.twig', ['redirect' => $redirect, 'last_username' => $lastUsername, 'error' => $error,  'form' => $form->createView()]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    /*  #[Route(path: '/2fa', name: '2fa_login')]
    public function check2fa()
    {
        return $this->render('security/2fa_form.html.twig');
    } */
}
