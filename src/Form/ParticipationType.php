<?php

namespace App\Form;

use App\Entity\Participation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Evenement;
use App\Entity\User;

class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('dateParticipation')
            ->add('descriptionParticipation')
            ->add('Evenement',EntityType::class,['class'=> Evenement::class,
            'choice_label'=>'nomEvenement',
            'label'=>'Nom Evenement'])
            ->add('User',EntityType::class,['class'=> User::class,
            'choice_label'=>'email',
            'label'=>'email User'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}


