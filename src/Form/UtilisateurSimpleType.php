<?php

namespace App\Form;

use App\Entity\UtilisateurSimple;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurSimpleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('nom')
            ->add('prenoms')
            ->add('residence', TextareaType::class, [
                'label' => 'Adresse de livraison'
            ])
            ->add('contact');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UtilisateurSimple::class,
        ]);
    }
}
