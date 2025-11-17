<?php

namespace App\Form;

use App\Entity\Coffre;
use App\Entity\Montre;
use App\Entity\Vitrine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MontreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('marque')
            ->add('reference')
            ->add('annee')
            ->add('coffre', null, [
                'disabled' => true,
            ])
            ->add('vitrines', EntityType::class, [
                'class' => Vitrine::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Montre::class,
        ]);
    }
}
