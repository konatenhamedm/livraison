<?php

namespace App\Form;

use App\DTO\InscriptionDTO;
use App\Entity\Civilite;
use App\Entity\Genre;
use App\Entity\Niveau;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('username', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Nom utilisateur'], 'required' => true,])

            ->add('email', EmailType::class, [
                'label' => false, 'attr' => ['placeholder' => 'Email (Login)'],
                'required' => true,

            ])
            ->add(
                'plainPassword',
                RepeatedType::class,
                [

                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'] ?? true,
                    'first_options'   => ['label' => false, 'attr' => ['placeholder' => 'Mot de passe']],
                    'second_options'  => ['label' => false, 'attr' => ['placeholder' => 'Repétez le mot de passe']],
                ]
            )
            /*  ->add('nom', TextType::class, ['label' => 'Nom', 'attr' => ['placeholder' => '']]) */
            /*  ->add('situation', TextType::class, ['label' => 'Situation géographique', 'attr' => ['placeholder' => '']]) */
            /* ->add('prenom', TextType::class, ['label' => 'Prénoms', 'attr' => ['placeholder' => '']]) */
            ->add('contact', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Contact(s)'], 'required' => true, 'empty_data' => ''])
            ->add('adresse', TextareaType::class, ['label' => false, 'attr' => ['placeholder' => 'Adresse de livraison par defaut'], 'required' => true, 'empty_data' => '']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here

        ]);
    }
}
