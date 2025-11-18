<?php

namespace App\Form;

use App\Entity\Coffre;
use App\Entity\Montre;
use App\Entity\Vitrine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
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
            ->add('imageFile', FileType::class, [
                'label' => 'Photo de la montre (JPG, PNG, WEBP, max 2 Mo)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez téléverser une image JPG, PNG ou WEBP valide.',
                    ]),
                ],
            ])
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
