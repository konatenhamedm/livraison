<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Employe;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('frais')
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
