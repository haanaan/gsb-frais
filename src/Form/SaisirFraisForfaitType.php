<?php

namespace App\Form;

use App\Entity\LigneFraisForfait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisirFraisForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantiteEtape', IntegerType::class, [
                'label' => 'Quantité étape',
                'attr' => [
                    'min' => 0,
                ]
            ])
            ->add('quantiteKm', IntegerType::class, [
                'label' => 'Nombre de kilomètres parcourus',
                'attr' => [
                    'min' => 0,
                ]
            ])
            ->add('quantiteNuit', IntegerType::class, [
                'label' => 'Nombre de nuitées',
                'attr' => [
                    'min' => 0,
                ]
            ])
            ->add('quantiteRepas', IntegerType::class, [
                'label' => 'Nombre de repas',
                'attr' => [
                    'min' => 0,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
