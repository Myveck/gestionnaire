<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Events;
use App\Entity\Filters;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
            ])
            ->add('Ville', EntityType::class, [
                'class' => Events::class,
                'choice_label' => 'city',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filters::class,
        ]);
    }
}
