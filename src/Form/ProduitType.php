<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\FournisseurProduit;
use App\Entity\Produit;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('poids')
            ->add('prix', TextType::class, [
                'attr' => ['class' => 'input-money input-mnt ']
            ])
            ->add('description', TextareaType::class, [])
            ->add(
                'fichier',
                FichierType::class,
                [
                    'label' => 'Image',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            )
            ->add('categorie', EntityType::class, [
                'required' => true,
                'class' => Categorie::class,
                'label' => 'Catégorie',
                'attr' => ['class' => 'has-select2 form-select'],
                'choice_label' => 'libelle',

            ])
            ->add('fournisseurProduits', CollectionType::class, [
                'entry_type' => FournisseurProduitType::class,
                'entry_options' => [
                    'label' => false,
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,

            ])

            ->add(
                'unite',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir une unité',
                    'label' => 'Privilèges Supplémentaires',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'K' => 'K',
                        'KG' => 'KG',
                    ]),
                ]
            );
        $builder->get('prix')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
