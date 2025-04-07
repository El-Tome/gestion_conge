<?php

namespace App\Form;

use App\Entity\Services;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SeuilAlerteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomService', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'nom',
                'label' => 'Service',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('seuilCritique', NumberType::class, [
                'label' => 'Seuil critique',
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['value' => 0]),
                ],
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
} 