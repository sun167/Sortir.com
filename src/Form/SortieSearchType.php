<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\SortieSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SortieSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus',EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un campus',
                'required' => false
            ])
            ->add('q', TextType::class, [
                'label' => 'Le nom de la sortie contient',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'placeholder' => 'search'
                ]
            ])
            ->add('premierDate', DateType::class, [
                'label' => 'Entre',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('deuxiemeDate', DateType::class, [
                'label' => 'et',
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
            ->add('organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur / trice",
                'required' => false
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
