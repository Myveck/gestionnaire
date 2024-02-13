<?php

namespace App\Form;

use App\Entity\Events;
use App\Entity\Participants;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'name',
                'label' => 'Nom du participant'
            ])
            ->add('event', EntityType::class, [
                'class' => Events::class,
                'choice_label' => 'name',
                'label' => 'Evénement'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
        ]);
    }
}
