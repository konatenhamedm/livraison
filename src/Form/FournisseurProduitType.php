<?php

namespace App\Form;

use App\Entity\Fournisseur;
use App\Entity\FournisseurProduit;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'input-money input-mnt ']
            ])
            ->add('fournisseur', EntityType::class, [
                'required' => true,
                'class' => Fournisseur::class,
                'label' => false,
                'attr' => ['class' => 'has-select2 form-select'],
                'choice_label' => 'libelle',

            ]);
        $builder->get('prix')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FournisseurProduit::class,
            'doc_required' => true
        ]);

        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
