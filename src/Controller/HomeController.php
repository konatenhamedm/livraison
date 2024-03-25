<?php

namespace App\Controller;

use App\DTO\InscriptionDTO;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\UserFront;
use App\Entity\UtilisateurSimple;
use App\Form\RegisterType;
use App\Form\UtilisateurType;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurSimpleRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\CartService;
use App\Service\FormError;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class HomeController extends AbstractController
{
    #[Route(path: '/home', name: 'app_default')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }
    #[Route(path: '/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $loginFormAuthenticator,
        FormError $formError,
        UtilisateurSimpleRepository $utilisateurRepository,

    ): Response {

        $inscriptionDTO = new InscriptionDTO();
        $form = $this->createForm(RegisterType::class, $inscriptionDTO, [
            'method' => 'POST',
            //'type'=>'autre',
            'action' => $this->generateUrl('app_register'),
        ]);

        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        //$redirect1 = $this->generateUrl("app_login_site");
        $fullRedirect = false;
        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl("app_site");

            $user = $utilisateurRepository->findOneByEmail($inscriptionDTO->getEmail());
            if ($form->isValid()) {

                if (!$user) {

                    /*   $utilisateur = new UserFront();
                    $utilisateur->setPassword($userPasswordHasher->hashPassword($utilisateur, $inscriptionDTO->getPlainPassword()));
                    $utilisateur->addRole('ROLE_CLIENT');
                    $utilisateur->setEmail($inscriptionDTO->getEmail());
                    $utilisateur->setUsername($inscriptionDTO->getEmail());

                    $entityManager->persist($utilisateur);
 */

                    $userSimple = new UtilisateurSimple();
                    $userSimple->setNom(strtoupper($inscriptionDTO->getNom()));
                    $userSimple->setPrenoms(ucwords($inscriptionDTO->getPrenom()));
                    $userSimple->setEmail($inscriptionDTO->getEmail());
                    $userSimple->setContact($inscriptionDTO->getContact());
                    $userSimple->setUsername($inscriptionDTO->getEmail());
                    $userSimple->addRole('ROLE_CLIENT');
                    $userSimple->setPassword($userPasswordHasher->hashPassword($userSimple, $inscriptionDTO->getPlainPassword()));

                    $entityManager->persist($userSimple);

                    $entityManager->flush();
                    $userAuthenticator->authenticateUser(
                        $userSimple,
                        $loginFormAuthenticator,
                        $request
                    );

                    $statut = 1;
                    $message = 'Compte crée avec succès';
                    $this->addFlash('success', 'Votre compte a été crée avec succès. Veuillez vous connecter pour continuer l\'opération vous pouvez consulter votre email');
                }
                $data = true;
                $fullRedirect = true;
                /* $statut = 1;
                $message = 'Compte crée avec succès';
                $this->addFlash('success', 'Votre compte a été crée avec succès. Veuillez vous connecter pour continuer l\'opération');*/
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }



            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('site/login/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route(path: '/login/site', name: 'app_login_site')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_site');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // return $this->render('security/login.html.twig', );

        // dd('login');

        return $this->render('site/login/index.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route(path: '/', name: 'app_site')]
    public function indexSite(ProduitRepository $produitRepository, CategorieRepository $categorieRepository, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach ($panierWithData as $couple) {
            $total += $couple['product']->getPrix() * $couple['quantity'];
        }

        return $this->render('site/index.html.twig', [
            'produits' => $produitRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            "items" => $panierWithData,
            'nombre' => count($panierWithData),
            "total" => $total,
            'vegetals' => $produitRepository->findBy(['categorie' => 3]),
        ]);
    }

    #[Route(path: '/panier', name: 'app_site_panier')]
    public function indexCart(ProduitRepository $produitRepository, CategorieRepository $categorieRepository, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }
        // dd($panierWithData);
        $total = 0;

        foreach ($panierWithData as $couple) {
            $total += $couple['product']->getPrix() * $couple['quantity'];
        }

        return $this->render('site/cart.html.twig', [
            'produits' => $produitRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            "items" => $panierWithData,
            'nombre' => count($panierWithData),
            "total" => $total
        ]);
    }


    #[Route(path: '/ajouter/{id}', name: 'cart_add')]
    public function add($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }

        $panier[$id]++;

        $session->set('panier', $panier);

        return $this->redirectToRoute("app_site");
    }


    #[Route(path: '/supprimer/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_site_panier');
    }
    #[Route(path: '/soumettre', name: 'cart_soumettre', methods: ['GET', 'POST'])]
    public function soummetre(SessionInterface $session, ProduitRepository $produitRepository, CommandeRepository $commandeRepository, LigneCommandeRepository $ligneCommandeRepository)
    {
        $panier = $session->get('panier', []);
        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = 0;

        foreach ($panierWithData as $couple) {
            $total += $couple['product']->getPrix() * $couple['quantity'];
        }


        $commande = new Commande();

        $commande->setLibelle('commande');
        $commande->setDateCommande(new DateTime());
        $commande->setTotal($total);

        $commandeRepository->save($commande, true);

        foreach ($panierWithData as $couple) {
            // $total += $couple['product']->getPrix() * $couple['quantity'];

            $ligne = new LigneCommande();

            $ligne->setCommande($commande);
            $ligne->setProduit($couple['product']);
            $ligne->setPrix($couple['product']->getPrix() * $couple['quantity']);
            $ligne->setEtat('recu_non_valide');
            $ligne->setQuantite($couple['quantity']);

            $ligneCommandeRepository->save($ligne, true);
        }




        foreach ($panierWithData as $couple) {

            /*  unset($panier[$couple['product']->getId()]); */

            if (!empty($panier[$couple['product']->getId()])) {
                unset($panier[$couple['product']->getId()]);
            }

            $session->set('panier', $panier);
        }


        return $this->redirectToRoute('app_site');
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
