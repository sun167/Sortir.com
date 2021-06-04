<?php

namespace App\Form;

use App\Entity\SortieSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus',ChoiceType::class, [
                'label' => 'Campus :',
                'required' => false,
                'choices' => [
                    'Colas' => 'Colas',
                    'Guillou' => 'Guillou',
                    'Chauvet' => 'Chauvet'
                ]
            ])
            ->add('q', TextType::class, [
                'label' => 'Le nom de la sortie contient :',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'placeholder' => 'search'
                ]
            ])
            ->add('premierDate', DateType::class, [
                'label' => 'Entre le :',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('deuxiemeDate', DateType::class, [
                'label' => 'Et le :',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('sortiePassee', CheckboxType::class, [
                'label' => 'Sorties Passées',
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
            ])
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
