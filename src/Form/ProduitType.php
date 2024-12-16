<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du produit',
                'attr' => ['placeholder' => 'Entrez le titre du produit'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Entrez une description détaillée (facultatif)'],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR', // Changez la devise si nécessaire
                'attr' => ['placeholder' => 'Entrez le prix du produit'],
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr' => ['placeholder' => 'Entrez la quantité disponible'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (fichier PNG, JPG ou JPEG)',
                'required' => false,
                'mapped' => false, // Ce champ n'est pas directement lié à l'entité
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
