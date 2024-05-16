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

class CommandeAttributionType extends AbstractType
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
            ->add('total', TextType::class, [
                //'label' => 'Montant commande',
                'attr' => ['class' => 'input-money input-mnt montant-input montant_echeancier']
            ])
            ->add('livreur', EntityType::class, [
                'class' => Employe::class,
                'required' => false,
                'placeholder' => '----',
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
        $builder->get('total')->addModelTransformer(new ThousandNumberTransformer());
        $builder->get('frais')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
