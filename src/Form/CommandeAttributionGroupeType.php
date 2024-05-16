<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Employe;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeAttributionGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateLivraison', DateType::class, [
                'label' => 'Date livraison',
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto skip-init'],
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'widget' => 'single_text',

            ])
            ->add('frais', TextType::class, [
                //'label' => 'Frais de livraison',
                'attr' => ['class' => 'input-money input-mnt montant-input montant_echeancier']
            ])
            ->add('commandes', EntityType::class, [
                'label'        => 'Commandes',
                'choice_label' => 'code',
                'multiple'     => true,
                'expanded'     => false,
                'placeholder' => 'Choisir une commande',
                'query_builder' => function (EntityRepository $er) {

                    return $er->createQueryBuilder('g')
                        ->andWhere('g.livreur is null');
                },
                'attr' => ['class' => 'has-select2 form-select element'],
                'class'        => Commande::class,
            ])
            ->add('livreur', EntityType::class, [
                'class' => Employe::class,
                'required' => true,
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->join('f.fonction', 'fonction')
                        ->andWhere('fonction.code = :code')
                        ->setParameter('code', 'LIVR');
                },
                'choice_label' => 'getNomComplet',
                'label' => 'Livreur',
                'attr' => ['class' => 'has-select2']
            ]);

        $builder->get('frais')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
