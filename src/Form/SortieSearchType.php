<?php

namespace App\Form;

use App\Entity\SortieSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus',ChoiceType::class, [
                'label' => 'Campus',
                'required' => false,
                'choices' => [
                    'Colas' => 'Colas',
                    'Guillou' => 'Guillou',
                    'Chauvet' => 'Chauvet'
                ]
            ])
            ->add('q', TextType::class, [
                'label' => 'Le nom de la sortie contient',
                'required' => false,
                'attr' => [
                    'placeholder' => 'search'
                ]
            ])
            ->add('sortiePassee', CheckboxType::class, [
                'label' => 'Sorties Passees',
                'required' => false,
            ])
//            ->add('premiereDate', DateTimeType::class, [
//                'label' => 'Entre',
//                'required' => false,
//            ])
//            ->add('deuxiemeDate', DateTimeType::class, [
//                'label' => 'et',
//                'required' => false,
//            ])
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SortieSearch::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
