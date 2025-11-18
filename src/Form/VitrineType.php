<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\Montre;
use App\Entity\Vitrine;
use App\Repository\MontreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VitrineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Vitrine|null $vitrine */
        $vitrine = $options['data'] ?? null;
        $member  = $vitrine?->getCreateur();  
        $builder
            ->add('description')
            ->add('publiee')
            ->add('createur', null, [
                'disabled' => true,   
            ]);

        $montresOptions = [
            'by_reference' => false,
            'multiple' => true,
            'expanded' => true,
        ];


        if ($member) {
            $montresOptions['query_builder'] = function (MontreRepository $er) use ($member) {
                return $er->createQueryBuilder('m')
                    ->leftJoin('m.coffre', 'c')
                    ->leftJoin('c.member', 'u')
                    ->andWhere('u.id = :memberId')
                    ->setParameter('memberId', $member->getId())
                    ->orderBy('m.marque', 'ASC')
                    ->addOrderBy('m.reference', 'ASC');
            };
        }

        $builder->add('montres', null, $montresOptions);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vitrine::class,
        ]);
    }
}
