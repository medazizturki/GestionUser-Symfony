<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Rendezvous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbScience')
            ->add('typeDePaiement',ChoiceType::class,[
            'label'=>'typeDePaiement',
            'choices' => [
                'carte bancaire' => 'carte bancaire',
                'espece' => 'espece',
                'carte e-dinar' => 'carte e-dinar',
                'virement bancaire and aziz' => 'virement bancaire and aziz',
            ],
            'expanded' => true,
            'multiple' => false,
  
            ])
            ->add('Rendezvous',EntityType::class,['class'=> Rendezvous::class,
            'choice_label'=>'getNomRendezvous',
            'label'=>'getNomRendezvous'
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
