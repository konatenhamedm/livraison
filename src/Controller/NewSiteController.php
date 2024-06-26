<?php

namespace App\Controller;

use App\DTO\InscriptionDTO;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Contact;
use App\Entity\Favorite;
use App\Entity\Produit;
use App\Entity\LigneCommande;
use App\Entity\UtilisateurSimple;
use App\Form\RegisterType;
use App\Form\UtilisateurSimpleType;
use App\Form\UtilisateurType;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\FavoriteRepository;
use App\Repository\UtilisateurSimpleRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\CartService;
use App\Service\FormError;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


#[Route(path: '/')]
class NewSiteController extends AbstractController
{
    #[Route(path: '', name: 'new_site')]
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

        $limitProduit = 5;
        $limitCategorie = 6;
        $limitPopulaire = 12;
        $limitProduitCategorie = 12;

        //dd($categorieRepository->findBy([], [], $limitCategorie));

        $populaires = $produitRepository->findAll();
        $populaires_index = $produitRepository->findBy([], [], $limitPopulaire);
        $recents_index = $produitRepository->findBy([], ['dateCreation' => 'DESC'], $limitPopulaire);
        $recents = $produitRepository->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('new_site/index.html.twig', [
            'produits_baniere' => $produitRepository->findBy([], ['dateCreation' => 'DESC'], $limitProduit),
            'produits' => $produitRepository->findAll(),
            'produits_index' => $produitRepository->findBy([], [], $limitProduitCategorie),
            'categories' => $categorieRepository->findAll(),
            'categories_index' => $categorieRepository->findBy([], [], $limitCategorie),
            "items" => $panierWithData,
            'nombre' => count($panierWithData),
            "total" => $total,
            'vegetals' => $produitRepository->findBy(['categorie' => 3]),
            'recents' => $recents,
            'recents_index' => $recents_index,
            'populaires_index' => $populaires_index,
            'populaires' => $populaires,
        ]);
    }

    #[Route(path: 'tous-les-produits', name: 'liste_produits')]
    public function liste_produits(Request $request, ProduitRepository $produitRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search', '');

        $nbrePerPage = 16;

        $produits = $produitRepository->findProduitsPaginatedAllProduct($page, $search, $nbrePerPage);

        return $this->render('new_site/liste_produits.html.twig', [
            'produits' => $produits,
            'search' => $search,
        ]);
    }

    #[Route('produit/{id}', name: 'detail_produit', methods: ['GET'])]
    public function detailProduit(Produit $produit)
    {
        $files = [
            "uploads/" . $produit->getFichier()->getPath() . '/' . $produit->getFichier()->getAlt(),
        ];

        foreach ($produit->getImages() as $image) {
            array_push($files, "uploads/" . $image->getFichier()->getPath() . '/' . $image->getFichier()->getAlt());
        }

        return $this->render('new_site/produit.html.twig', [
            'produit' => $produit
        ]);

        return $this->json([
            'data' =>
            [
                "produit" => [
                    'id' => $produit->getId(),
                    'libelle' => $produit->getLibelle(),
                    'description' => $produit->getDescription(),
                    'prix' => $produit->getPrix(),
                    'categorie_id' => $produit->getCategorie()->getId(),
                    'categorie_libelle' => $produit->getCategorie()->getLibelle(),
                    'images' => $files
                ],
            ]
        ]);
    }


    #[Route(path: 'toutes-les-categories', name: 'liste_categories')]
    public function liste_categories(CategorieRepository $categorieRepository): Response
    {
        return $this->render('new_site/liste_categories.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }



    #[Route('categorie/{id}', name: 'detail_categorie', methods: ['GET'])]
    public function detailCategorie(Categorie $categorie, CategorieRepository $categorieRepository)
    {

        return $this->render('new_site/categorie.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categorie' => $categorie
        ]);
    }


    #[Route(path: 'authentification', name: 'connexion_inscription')]
    public function connexion_inscription(Request $request): Response
    {
        return $this->render('new_site/authentification.html.twig');
    }

    #[Route(path: 'contact', name: 'contact')]
    public function contact(Request $request): Response
    {
        return $this->render('new_site/contact.html.twig');
    }

    #[Route(path: 'resume', name: 'resume')]
    public function resume(
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        SessionInterface $session,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $loginFormAuthenticator,
        FormError $formError,
        UtilisateurSimpleRepository $utilisateurRepository,
        AuthenticationUtils $authenticationUtils
    ): Response {
        $panier = $session->get('panier', []);
        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantity
            ];
        }
        // dd($panierWithData);
        $sousTotal = $total = 0;

        foreach ($panierWithData as $couple) {
            $sousTotal += $couple['produit']->getPrix() * $couple['quantite'];
        }

        $livraison = 0;

        $total = $sousTotal + $livraison;


        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

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
            $redirect = $this->generateUrl("new_site");

            $user = $utilisateurRepository->findOneByEmail($inscriptionDTO->getEmail());
            if ($form->isValid()) {

                if (!$user) {

                    $userSimple = new UtilisateurSimple();
                    $userSimple->setNom(strtoupper($inscriptionDTO->getUsername()));
                    $userSimple->setPrenoms(ucwords($inscriptionDTO->getUsername()));
                    $userSimple->setEmail($inscriptionDTO->getEmail());
                    $userSimple->setContact($inscriptionDTO->getContact());
                    $userSimple->setResidence($inscriptionDTO->getResidence());
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
                //dd($data);
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('new_site/resume.html.twig', [
            "items" => $panierWithData,
            'nombre' => count($panierWithData),
            "sousTotal" => $sousTotal,
            "total" => $total,
            'form' => $form->createView(),
            'last_username' => $lastUsername, 'error' => $error,
        ]);
    }

    #[Route(path: 'panier', name: 'panier')]
    public function indexCart(ProduitRepository $produitRepository, CategorieRepository $categorieRepository, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantity
            ];
        }
        // dd($panierWithData);
        $total = 0;

        foreach ($panierWithData as $couple) {
            $total += $couple['produit']->getPrix() * $couple['quantite'];
        }

        return $this->render('new_site/panier.html.twig', [
            'produits' => $produitRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            "items" => $panierWithData,
            'nombre' => count($panierWithData),
            "total" => $total
        ]);
    }

    #[Route(path: 'panier/ajouter/{id}', name: 'ajouter_au_panier', methods: ['POST'])]
    public function addCart($id, ProduitRepository $produitRepository, SessionInterface $session, Request $request)
    {
        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }
        // dd($request->get());
        if ($quantite = $request->get('quantite')) {
            $panier[$id] = $quantite;
        } else {
            $panier[$id]++;
        }

        $montant = 0;
        foreach ($panier as $id => $quantity) {
            $montant += $quantity * $produitRepository->find($id)->getPrix();
        }

        $session->set('panier', $panier);
        $session->set('total_panier', $montant);


        // return $this->redirectToRoute("app_site");

        return $this->json([
            'data' =>
            [
                "panierNbre" => count($panier),
                "panierMontant" => $montant
            ]
        ]);
    }


    #[Route(path: 'panier/supprimer/{id}', name: 'supprimer_du_panier')]
    public function removeCart($id, ProduitRepository $produitRepository, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        $montant = $session->get('total_panier') - ($panier[$id] * $produitRepository->find($id)->getPrix());

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }


        $session->set('panier', $panier);
        $session->set('total_panier', $montant);

        // return $this->redirectToRoute('app_site_panier');

        return $this->json([
            'data' =>
            [
                "panierNbre" => count($panier),
                "panierMontant" => $session->get('total_panier')
            ]
        ]);
    }

    #[Route(path: 'panier/vider', name: 'vider_le_panier')]
    public function emptyCart(SessionInterface $session)
    {
        $panier = [];

        $session->set('panier', $panier);
        $session->set('total_panier', 0);

        // return $this->redirectToRoute('app_site_panier');

        return $this->json([
            'data' =>
            [
                "panierNbre" => count($panier),
                "panierMontant" => 0
            ]
        ]);
    }


    #[Route(path: 'mon-compte', name: 'dashboard')]
    public function dashboardAccount(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_auth_simple', ['redirect' => $this->generateUrl('account')]);
        }

        return $this->render('new_site/account/dashboard.html.twig', []);
    }

    #[Route(path: 'mon-compte/commandes', name: 'orders')]
    public function indexOrders(Request $request, FavoriteRepository $favoriteRepository, CommandeRepository $commandeRepository, UserInterface $userInterface): Response
    {

        // dd($userInterface->getId);
        // dd();

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_auth_simple', ['redirect' => $this->generateUrl('orders')]);
        }

        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search', '');

        $nbrePerPage = 16;

        $orders = [
            'data' => $commandeRepository->findCommandeUserPaginated($page, $this->getUser()->getEmail(), 12),
            'pages' => 1,
            'page' => 1,
            'limit' => 1
        ];

        // dd($orders);
        // $favoriteRepository->findProduitsPaginatedFavorites($page, $search, $nbrePerPage);


        return $this->render('new_site/account/orders.html.twig', [
            'orders' => $orders,
            'search' => $search,
        ]);
    }

    #[Route(path: 'mon-compte/favoris', name: 'favorites')]
    public function indexFavorites(Request $request, FavoriteRepository $favoriteRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_auth_simple', ['redirect' => $this->generateUrl('favorites')]);
        }

        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search', '');

        $nbrePerPage = 16;

        $favorites = $favoriteRepository->findProduitsPaginatedFavorites($page, $search, $nbrePerPage);


        return $this->render('new_site/account/favorites.html.twig', [
            'favorites' => $favorites,
            'search' => $search,
        ]);
    }

    #[Route(path: 'favoris/ajouter/{produit}', name: 'ajouter_aux_favoris')]
    public function addFavorites(Produit $produit, FavoriteRepository $favoriteRepository, Request $request)
    {
        if (!$this->getUser()) {
            // return $this->redirectToRoute('app_auth_simple', ['redirect' => $this->generateUrl('ajouter_aux_favoris', ['produit' => $produit.id])]);
            return $this->redirectToRoute('app_auth_simple');
        }

        $favorite = $favoriteRepository->findOneBy(['produit' => $produit, 'userFront' => $this->getUser()]);
        if (!$favorite) {
            $favorite = new Favorite();
            $favorite->setProduit($produit);
            $favorite->setUserFront($this->getUser());
            $favorite->setDateFav(date_create());

            $favoriteRepository->save($favorite, true);
        } else {
            $favoriteRepository->remove($favorite, true);
        }

        return $this->redirectToRoute('favorites');
    }


    #[Route(path: 'favoris/supprimer/{produit}', name: 'supprimer_des_favoris')]
    public function removeFavorites(Produit $produit, FavoriteRepository $favoriteRepository, SessionInterface $session)
    {
        if (!$this->getUser()) {
            // return $this->redirectToRoute('app_auth_simple', ['redirect' => $this->generateUrl('ajouter_aux_favoris', ['produit' => $produit.id])]);
            return $this->redirectToRoute('app_auth_simple');
        }

        $favorite = $favoriteRepository->findOneBy(['produit' => $produit, 'userFront' => $this->getUser()]);
        if (!$favorite) {
            $favorite = new Favorite();
            $favorite->setProduit($produit);
            $favorite->setUserFront($this->getUser());
            $favorite->setDateFav(date_create());

            $favoriteRepository->save($favorite, true);
        } else {
            $favoriteRepository->remove($favorite, true);
        }

        return $this->redirectToRoute('favorites');
    }


    #[Route(path: 'mon-compte/informations-personnelles', name: 'informations')]
    public function indexPersonnal(Request $request, FavoriteRepository $favoriteRepository,  FormError $formError, UtilisateurSimpleRepository $utilisateurSimpleRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_auth_simple', ['redirect' => $this->generateUrl('informations')]);
        }

        $user = $this->getUser();

        $form = $this->createForm(UtilisateurSimpleType::class, $user, [
            'method' => 'POST',
            //'type'=>'autre',
            'action' => $this->generateUrl('informations'),
        ]);

        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        //$redirect1 = $this->generateUrl("app_login_site");
        $fullRedirect = false;
        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl("informations");

            // $user = $utilisateurRepository->findOneByEmail($inscriptionDTO->getEmail());
            if ($form->isValid()) {


                $utilisateurSimpleRepository->save($user, true);

                $data = true;
                $fullRedirect = true;
                $statut = 1;
                $message = 'Compte crée avec succès';
                $this->addFlash('success', 'Votre compte a été crée avec succès. Veuillez vous connecter pour continuer l\'opération');
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
                // dd("");
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        return $this->render('new_site/account/informations.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route(path: '/home', name: 'app_default')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }
    #[Route(path: '/register', name: 'app_register_new', methods: ['GET', 'POST'])]
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
            'action' => $this->generateUrl('app_register_new'),
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
                    /* $userSimple->setNom(strtoupper($inscriptionDTO->getNom()));
                    $userSimple->setPrenoms(ucwords($inscriptionDTO->getPrenom())); */
                    $userSimple->setEmail($inscriptionDTO->getEmail());
                    $userSimple->setContact($inscriptionDTO->getContact());
                    $userSimple->setResidence($inscriptionDTO->getResidence());
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







    private function numero($code)
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Commande::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ($code . '-' . date("y") . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
    private function genererLibelleCommmande()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Commande::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ('Commande' . ' n° ' . str_pad($nb, 3, '0', STR_PAD_LEFT)) . '-' . date("m") . '-' . date("y");
    }
    #[Route(path: 'soumettre', name: 'cart_soumettre', methods: ['GET', 'POST'])]
    public function soummetre(Request $request, SessionInterface $session, ProduitRepository $produitRepository, CommandeRepository $commandeRepository, LigneCommandeRepository $ligneCommandeRepository)
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

        // dd($this->getUser()->getResidence(), $request->request->get('lieu'));

        $commande = new Commande();

        $commande->setLibelle($this->genererLibelleCommmande());
        $commande->setUtilisateur($this->getUser());
        if ($request->request->get('lieu') == "") {

            $commande->setLieu($this->getUser()->getResidence());
        } else {

            $commande->setLieu($request->request->get('lieu'));
        }
        $commande->setDateCommande(new DateTime());
        $commande->setCode($this->numero('CM'));
        $commande->setEtat(Commande::ETAPES['commande_non_traiter']);
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

        return $this->json([
            'success' => True
        ]);

        return $this->redirectToRoute('new_site');
    }

    #[Route('/commande-validée', name: 'commande_validee', methods: ['GET', 'POST'])]
    public function commandeValidee(Request $request): Response
    {
        return $this->render('new_site/commande_validee.html.twig', []);
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

    #[Route(path: 'soumettre/contact', name: 'send_contact', methods: ['GET', 'POST'])]
    public function sendContact(Request $request, EntityManagerInterface $entityManager, FormError $formError)
    {
        // $response = [];


        //dd("");
        // dd($request->get('nom'));
        if (
            $request->request->get('nom') != null && $request->request->get('email')  != null && $request->request->get('sujet')  != null
            && $request->request->get('message')  != null
            && $request->request->get('telephone')  != null

        ) {
            $contact = new Contact();
            $contact->setTelephone($request->request->get('telephone'));
            $contact->setNom($request->request->get('nom'));
            $contact->setEmail($request->request->get('email'));
            $contact->setSujet($request->request->get('sujet'));
            $contact->setMessage($request->request->get('message'));

            $entityManager->persist($contact);
            $entityManager->flush();
            $response['success'] = true;
            $response['message'] = "Merci pour votre message";
        } else {
            $response['success'] = false;
            $response['message'] = "S'il vous plait, veuillez remplir tous les champs";
        }



        return   $this->json($response);
    }
}
