<?php

namespace App\Controller;

use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    #[Route(path: '/home', name: 'app_default')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/error_page', name: 'page_error_index', methods: ['GET', 'POST'])]
    public function errorIndex(Request $request): Response
    {
        return $this->render('error.html.twig', []);
    }

    private $em;
    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }


    #[Route(path: '/print-iframe', name: 'default_print_iframe', methods: ["DELETE", "GET"], condition: "request.query.get('r')", options: ["expose" => true])]
    public function defaultPrintIframe(Request $request, UrlGeneratorInterface $urlGenerator)
    {
        $all = $request->query->all();
        //print-iframe?r=foo_bar_foo&params[']
        $routeName = $request->query->get('r');
        $title = $request->query->get('title');
        $params = $all['params'] ?? [];
        $stacked = $params['stacked'] ?? false;
        $redirect = isset($params['redirect']) ? $urlGenerator->generate($params['redirect'], $params) : '';
        $iframeUrl = $urlGenerator->generate($routeName, $params);

        $isFacture = isset($params['mode']) && $params['mode'] == 'facture' && $routeName == 'facturation_facture_print';

        return $this->render('home/iframe.html.twig', [
            'iframe_url' => $iframeUrl,
            'id' => $params['id'] ?? null,
            'stacked' => $stacked,
            'redirect' => $redirect,
            'title' => $title,
            'facture' => 0/*$isFacture*/
        ]);
    }
}
