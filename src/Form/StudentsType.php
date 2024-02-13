<?php

namespace App\Form;

use App\Entity\Students;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class,[
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class,[
                'label' => 'Nom'
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email'
            ])
            ->add('city', TextType::class,[
                'label' => 'Ville'
            ])
            ->add('telephone', TextType::class,[
                'label' => 'Téléphone'
            ])
            ->add('quater', TextType::class,[
                'label' => 'Quatier'
            ])
            ->add('study_year', IntegerType::class,[
                'label' => 'Année'
            ])
            ->add('birthday', DateType::class,[
                'label' => 'Date de naissance'
            ])
            ->add('sector', TextType::class,[
                'label' => 'Filière'
            ])
            ->add('study_option', TextType::class,[
                'label' => 'Option'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Students::class,
        ]);
    }
}
