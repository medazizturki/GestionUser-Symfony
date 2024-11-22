<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Ressource;
use App\Entity\User;


class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut')
             ->add('dateFin') 
            ->add('descriptionReservation')
            ->add('User',EntityType::class,['class'=> User::class,
           'choice_label'=>'name',
           'label'=>'Medecin'])
            ->add('Ressource',EntityType::class,['class'=> Ressource::class,
           'choice_label'=>'id',
           'label'=>'Ressource']);

            
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
    
}


