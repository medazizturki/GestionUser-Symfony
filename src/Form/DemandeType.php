<?php

namespace App\Form;


use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;
use App\Entity\Offre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('User',EntityType::class,['class'=> User::class,
        'choice_label'=>'email',
        'label'=>'emailUser'])
            
       
        #->add('cv')
        ->add('cv', FileType::class, [
            // unmapped means that this field is not associated to any entity property
            'data_class' => null,
            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            
            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                        
                    ],
                    'mimeTypesMessage' => 'Please upload a valid Image',
                ])
            ]])
        ->add('description')
        
      
       
        ->add('Offre',EntityType::class,['class'=> Offre::class,
       'choice_label'=>'nomOffre',
       'label'=>'nomOffre']
       )
       ->add('traitement', HiddenType::class, [
        'data' => 'en cours de traitement',
    ])
       
        
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
