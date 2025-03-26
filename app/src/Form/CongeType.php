<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('type', ChoiceType::class, [
                'label'    => 'Type de congé',
                'required' => true,
                'choices'  => [
                    'Congé annuel'              => 'Congé annuel',
                    'Congé maladie'             => 'Congé maladie',
                    'Congé sans solde'          => 'Congé sans solde',
                    'Congé maternité/paternité' => 'Congé maternité/paternité',
                    'RTT'                       => 'RTT',
                    'Congé sabbatique'          => 'Congé sabbatique',
                    'Autre'                     => 'Autre',
                ],
                'attr'     => [
                    'class' => 'form-control',
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}