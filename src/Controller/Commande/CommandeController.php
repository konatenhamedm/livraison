<?php

namespace App\Controller\Commande;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Entity\Versement;
use App\Form\CommandeAttributionGroupeType;
use App\Form\CommandeAttributionType;
use App\Form\CommandeValidationType;
use App\Form\CommandeVersementType;
use App\Service\Omines\Column\NumberFormatColumn;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

#[Route('/ads/commande/commande')]
class CommandeController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_commande_commande_index';

    #[Route('/{id}/imprime', name: 'app_comptabilite_print', methods: ['GET'])]
    public function imprimer($id, Commande $commande): Response
    {

        // $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("commande/commande/recu.html.twig", [
            'data' => $commande,
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'L',
            'protected' => true,

            'format' => 'A5',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' => false,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

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

    #[Route('/{etat}', name: 'app_commande_commande_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, $etat): Response
    {

        //dd($etat);
        if ($etat == 'commande_non_traiter') {
            $titre = 'Liste des commandes non traitées';
        } elseif ($etat == 'commande_attribuer') {
            $titre = 'Liste des commandes attribuées';
        } elseif ($etat == 'commande_livrer') {
            $titre = 'Liste des commandes livrées';
        }

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('lieu', TextColumn::class, ['label' => 'Lieu de livraison'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('dateCommande', DateTimeColumn::class, ['label' => 'Date commande', 'format' => 'd-m-Y'])
            ->add('total', NumberFormatColumn::class, ['label' => 'Montant commande'])
            ->add('frais', NumberFormatColumn::class, ['label' => 'Montant frais'])
            ->add('versement', TextColumn::class, ['label' => 'Etat versement', 'visible' => false, 'render' => function ($value, Commande $context) {
                return $context->getVersements()->count() > 0 ? 'Versement fait' : 'Pas de versement';
            }])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Commande::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select(['c'])
                        ->from(Commande::class, 'c')
                        ->where('c.etat = :etat')
                        ->setParameter('etat', $etat)
                        ->orderBy('c.dateCommande', 'DESC');
                }
            ])
            ->setName('dt_app_commande_commande' . $etat);
        if ($permission != null) {

            $renders = [
                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'attribuer' => new ActionRender(function () use ($permission, $etat) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU' && $etat == 'commande_non_traiter') {
                        return true;
                    } elseif ($permission == 'CRUD' && $etat == 'commande_non_traiter') {
                        return true;
                    } elseif ($permission == 'CRU' && $etat == 'commande_non_traiter') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'desattribuer' => new ActionRender(function () use ($permission, $etat) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU' && $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CRUD' && $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CRU' && $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'valider' => new ActionRender(function () use ($permission, $etat) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU' && $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CRUD' && $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CRU' && $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission, $etat) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD' && $etat == 'commande_non_traiter' || $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'RU' && $etat == 'commande_non_traiter' || $etat == 'commande_attribuer') {
                        return false;
                    } elseif ($permission == 'CRUD' && $etat == 'commande_non_traiter' || $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission, $etat) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD' && $etat == 'commande_livrer' || $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'RU' && $etat == 'commande_livrer' || $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CRUD' && $etat == 'commande_livrer' || $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CRU' && $etat == 'commande_livrer' || $etat == 'commande_attribuer') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                }),

            ];


            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Commande $context) use ($renders, $etat) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                /* 'edit' => [
                                    'url' => $this->generateUrl('app_commande_commande_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ], */
                                'attribuer' => [
                                    'url' => $this->generateUrl('app_commande_commande_attribuer', ['id' => $value, 'type' => 'attribuer']), 'ajax' => true, 'icon' => '%icon% bi bi-share ', 'attrs' => ['class' => 'btn-success'], 'title' => 'Attribuer un livreur', 'render' => $renders['attribuer']
                                ],
                                'desattribuer' => [
                                    'url' => $this->generateUrl('app_commande_commande_attribuer', ['id' => $value, 'type' => 'desattribuer']), 'ajax' => true, 'icon' => '%icon% bi bi-lock ', 'attrs' => ['class' => 'btn-warning'], 'title' => 'Desattribuer le livreur', 'render' => $renders['desattribuer']
                                ],
                                'valider' => [
                                    'url' => $this->generateUrl('app_commande_commande_valider', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-check ', 'attrs' => ['class' => 'btn-success'], 'render' => $renders['valider']
                                ],
                                'imprime' => [
                                    'url' => $this->generateUrl('default_print_iframe', [
                                        'r' => 'app_comptabilite_print',
                                        'params' => [
                                            'id' => $value,
                                        ]
                                    ]),
                                    'ajax' => true,
                                    'target' =>  '#exampleModalSizeSm2',
                                    'icon' => '%icon% bi bi-printer',
                                    'attrs' => ['class' => 'btn-main btn-stack'],
                                    'render' => new ActionRender(fn () => $etat == 'commande_non_traiter')
                                ],
                                'versement' => [
                                    'url' => $this->generateUrl('app_commande_commande_versement', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-cash ', 'attrs' => ['class' => 'btn-success'],
                                    'render' => new ActionRender(fn () => $etat == 'commande_livrer')
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_commande_commande_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_commande_commande_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-danger'], 'render' => $renders['delete']
                                ]
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('commande/commande/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat,
            'titre' => $titre,

        ]);
    }

    #[Route('/new', name: 'app_commande_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_commande_commande_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_livraison_index');


            if ($form->isValid()) {

                $entityManager->persist($commande);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('commande/commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_commande_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(CommandeType::class, $commande, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_commande_commande_edit', [
                'id' => $commande->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_livraison_index');


            if ($form->isValid()) {

                $entityManager->persist($commande);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('commande/commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/valider', name: 'app_commande_commande_valider', methods: ['GET', 'POST'])]
    public function validation(Request $request, Commande $commande, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $commande->setDateValidationLivraison(new \DateTime());
        $form = $this->createForm(CommandeValidationType::class, $commande, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_commande_commande_valider', [
                'id' => $commande->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_livraison_index');


            if ($form->isValid()) {

                $commande->setEtat(Commande::ETAPES['commande_livrer']);
                $entityManager->persist($commande);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('commande/commande/validation.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/versement', name: 'app_commande_commande_versement', methods: ['GET', 'POST'])]
    public function versement(Request $request, Commande $commande, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(CommandeVersementType::class, null, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_commande_commande_versement', [
                'id' => $commande->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_livraison_index');

            //dd($commande->getVersements()->count());

            if ($form->isValid()) {


                if ($commande->getVersements()->count() == 0) {
                    $versement = new Versement();
                    $versement->setCommande($commande);
                    $versement->setMontant($form->get('montant')->getData());
                    $versement->setDateVersement($form->get('dateVersement')->getData());

                    if (((int)$commande->getTotal() + (int)$commande->getFrais()) == (int)$form->get('montant')->getData()) {
                        $entityManager->persist($versement);
                        $entityManager->flush();
                        $message = 'Opération effectuée avec succès';
                        $statut = 1;
                    } else {
                        $message = sprintf('Opération à échouées car le montant saisi %s est different du montant total %s à verser', $form->get('montant')->getData(), $commande->getTotal());
                        $statut = 0;
                    }
                } else {
                    $message = 'Opération à échouées car la commande a deja un versement';
                    $statut = 0;
                }

                $data = true;

                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('commande/commande/versement.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/{type}/attribuer', name: 'app_commande_commande_attribuer', methods: ['GET', 'POST'])]
    public function attribuer(Request $request, Commande $commande, EntityManagerInterface $entityManager, FormError $formError, $type): Response
    {
        if ($commande->getDateLivraison() == null) {
            $commande->setDateLivraison(new \DateTime());
        }

        $form = $this->createForm(CommandeAttributionType::class, $commande, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_commande_commande_attribuer', [
                'id' => $commande->getId(),
                'type' => $type
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_livraison_index');


            if ($form->isValid()) {

                if ($type == 'desattribuer') {
                    $commande->setLivreur(null);
                    $commande->setDateLivraison(null);
                    $commande->setFrais(0);
                    $commande->setEtat(Commande::ETAPES['commande_non_traiter']);
                }
                if ($type == 'attribuer' && $commande->getLivreur() == null) {
                    $message = 'Opération à échouée car vous n\'avez pas  attribuée un livreur';
                    $statut = 0;
                } else {
                    $message = 'Opération effectuée avec succès';
                    $statut = 1;
                }

                $entityManager->persist($commande);
                $entityManager->flush();

                $data = true;

                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('commande/commande/attribuer.html.twig', [
            'commande' => $commande,
            'form' => $form,
            'type' => $type
        ]);
    }
    #[Route('/attribuer/groupe', name: 'app_commande_commande_attribuer_groupe', methods: ['GET', 'POST'])]
    public function attribuerGroupe(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        //$commande->setDateLivraison(new \DateTime());
        $form = $this->createForm(CommandeAttributionGroupeType::class, null, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_commande_commande_attribuer_groupe')
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_livraison_index');

            //dd($form->get('commandes')->getData());


            if ($form->isValid()) {

                foreach ($form->get('commandes')->getData() as $key => $commande) {
                    $commande->setDateLivraison($form->get('dateLivraison')->getData());
                    $commande->setLivreur($form->get('livreur')->getData());
                    $commande->setFrais($form->get('frais')->getData());
                    $commande->setEtat('commande_attribuer');
                    $entityManager->persist($commande);
                    $entityManager->flush();
                }


                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('commande/commande/attribuer_groupe.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_commande_commande_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_commande_commande_delete',
                    [
                        'id' => $commande->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($commande);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_config_parametre_livraison_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut' => 1,
                'message' => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('commande/commande/delete.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }
}
