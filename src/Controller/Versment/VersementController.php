<?php

namespace App\Controller\Versment;

use App\Entity\Versement;
use App\Form\VersementType;
use App\Repository\VersementRepository;
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
use App\Service\Omines\Column\NumberFormatColumn;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\DateType;

#[Route('/ads/versment/versement')]
class VersementController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_versment_versement_index';

    #[Route('/imprime/all/{dateDebut}/{dateFin}/point des versements', name: 'app_comptabilite_print_all', methods: ['GET', 'POST'])]
    public function imprimerAll(Request $request, $dateDebut, $dateFin, VersementRepository $versementRepository): Response
    {

        // $niveau = $request->query->get('niveau');


        $totalImpaye = 0;
        $totalPayer = 0;

        //dd($dateNiveau);
        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'lg.jpeg';
        return $this->renderPdf("versment/versement/recu.html.twig", [
            'total_payer' => $totalPayer,
            'data' => $versementRepository->searchResult($dateDebut, $dateFin),
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'p',
            'protected' => true,
            'file_name' => "point_versments",

            'format' => 'A4',

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

    #[Route('/', name: 'app_versment_versement_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');

        $etat = "all";

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_versment_versement_index', compact('dateDebut', 'dateFin'))
        ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ]);


        $table = $dataTableFactory->create()
            ->add('code',  TextColumn::class, ['label' => 'Code commande', 'field' => 'c.code'])
            ->add('dateVersement',  DateTimeColumn::class, ['label' => 'Date Versement', 'format' => 'd-m-Y', 'searchable' => false])
            ->add('montant',  NumberFormatColumn::class, ['label' => 'Montant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Versement::class,
                'query' => function (QueryBuilder $qb) use ($dateDebut, $dateFin) {
                    $qb->select(['p', 'c'])
                        ->from(Versement::class, 'p')
                        ->leftJoin('p.commande', 'c')
                        ->orderBy('p.dateVersement', 'DESC');

                    if ($dateDebut || $dateFin) {


                        if ($dateDebut && $dateFin == null) {
                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('p.dateVersement = :dateDebut')
                                ->setParameter('dateDebut', $new_date_debut);
                        }
                        if ($dateFin && $dateDebut == null) {

                            $truc = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];

                            $qb->andWhere('p.dateVersement  = :dateFin')
                                ->setParameter('dateFin', $new_date_fin);
                        }
                        if ($dateDebut && $dateFin) {

                            $truc_debut = explode('-', str_replace("/", "-", $dateDebut));
                            $new_date_debut = $truc_debut[2] . '-' . $truc_debut[1] . '-' . $truc_debut[0];

                            $truc = explode('-', str_replace("/", "-", $dateFin));
                            $new_date_fin = $truc[2] . '-' . $truc[1] . '-' . $truc[0];
                            // dd($new_date_debut, $new_date_fin);

                            $qb->andWhere('p.dateVersement BETWEEN :dateDebut AND :dateFin')
                                ->setParameter('dateDebut', $new_date_debut)
                                ->setParameter("dateFin", $new_date_fin);
                        }
                    }
                }
            ])
            ->setName('dt_app_versment_versement_');
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
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                }),

            ];
            $gridId = '';

            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = false;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Versement $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                /* 'edit' => [
                                    'url' => $this->generateUrl('app_versment_versement_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_versment_versement_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_versment_versement_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                                ] */]

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


        return $this->render('versment/versement/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'form' => $builder->getForm()->createView(),
            'grid_id' => $gridId,
            "etat" => $etat
        ]);
    }

    #[Route('/new', name: 'app_versment_versement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $versement = new Versement();
        $form = $this->createForm(VersementType::class, $versement, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_versment_versement_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_versment_versement_index');


            if ($form->isValid()) {

                $entityManager->persist($versement);
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

        return $this->renderForm('versment/versement/new.html.twig', [
            'versement' => $versement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_versment_versement_show', methods: ['GET'])]
    public function show(Versement $versement): Response
    {
        return $this->render('versment/versement/show.html.twig', [
            'versement' => $versement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_versment_versement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Versement $versement, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(VersementType::class, $versement, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_versment_versement_edit', [
                'id' => $versement->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_versment_versement_index');


            if ($form->isValid()) {

                $entityManager->persist($versement);
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

        return $this->renderForm('versment/versement/edit.html.twig', [
            'versement' => $versement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_versment_versement_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Versement $versement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_versment_versement_delete',
                    [
                        'id' => $versement->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($versement);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_versment_versement_index');

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

        return $this->renderForm('versment/versement/delete.html.twig', [
            'versement' => $versement,
            'form' => $form,
        ]);
    }
}
