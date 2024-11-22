<?php

namespace App\Form;

use App\Entity\Rendezvous;
use App\Entity\user;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RendezvousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomRendezvous')
            ->add('prenomRendezvous')
            ->add('lieuRendezvous')
            ->add('emailRendezvous')
            ->add('dateRendezvous')
            ->add('color', HiddenType::class, [
                'data' => '#000000',
            ])
               
           
            ->add('User',EntityType::class,['class'=> User::class,
           'choice_label'=>'name',
           'label'=>'name']);

            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendezvous::class,
        ]);
    }
}
