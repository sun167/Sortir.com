<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, ['label' => 'Nom de la sortie : '])
            ->add('description', null, ['label' => 'Description : '])
            ->add('dateDebut', DateTimeType::class)
            ->add('dateFin', DateTimeType::class)
            ->add('duree', null, ['label' => 'Durée : '])
            ->add('nbInscriptionsMax', null, ['label' => 'Nombre d\'inscription max : '])
            ->add('urlPhoto',  FileType::class, ['mapped' =>false, 'required' => false, 'constraints' => [new Image(['maxSize' => '7024k', 'mimeTypesMessage' => "Format de l'image non supporter"])]])
            ->add('lieu', EntityType::class, ['class' => Lieu::class, 'choice_label' => 'nom'])
            ->add('campus', EntityType::class, ['class' => Campus::class, 'choice_label' => 'nom'])
//            ->add('etat', EntityType::class, ['class' => Etat::class, 'choice_label' => 'libelle'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
