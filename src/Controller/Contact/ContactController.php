<?php

namespace App\Controller\Contact;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
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
use Doctrine\ORM\EntityManagerInterface;

#[Route('/ads/contact/contact')]
class ContactController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_contact_contact_index';

    #[Route('/', name: 'app_contact_contact_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => 'Identifiant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Contact::class,
            ])
            ->setName('dt_app_contact_contact');
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


            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Contact $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_contact_contact_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_contact_contact_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_contact_contact_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('contact/contact/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }



    #[Route(path: '/soumettre/contact', name: 'soumettre_contact', methods: ['GET', 'POST'])]
    public function soummetre(Request $request, EntityManagerInterface $entityManager, FormError $formError)
    {
        $response = [];


        // dd("");
        /*  dd($request->get('nom'));
        if (
            $request->get('nom') != null && $request->get('email')  != null && $request->get('sujet')  != null
            && $request->get('message')  != null
            && $request->get('telephone')  != null

        ) { */
        $contact = new Contact();
        $contact->setTelephone($request->get('telephone'));
        $contact->setNom($request->get('nom'));
        $contact->setEmail($request->get('email'));
        $contact->setSujet($request->get('sujet'));
        $contact->setMessage($request->get('message'));

        $entityManager->persist($contact);
        $entityManager->flush();
        $response['success'] = true;
        /* } else {
            $response['success'] = true;
        } */



        return   $this->json([
            'success' => True
        ]);
    }

    #[Route('/new', name: 'app_contact_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_contact_contact_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_contact_contact_index');


            if ($form->isValid()) {

                $entityManager->persist($contact);
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

        return $this->renderForm('contact/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_contact_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contact_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(ContactType::class, $contact, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_contact_contact_edit', [
                'id' => $contact->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_contact_contact_index');


            if ($form->isValid()) {

                $entityManager->persist($contact);
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

        return $this->renderForm('contact/contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_contact_contact_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_contact_contact_delete',
                    [
                        'id' => $contact->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($contact);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_contact_contact_index');

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

        return $this->renderForm('contact/contact/delete.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
