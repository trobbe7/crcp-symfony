<?php

namespace App\Form;

use App\Entity\Objectifs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class ObjectifsAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('obj90', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un objectif.',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]\d*[.,:]?\d{0,2}$/',
                        'message' => 'Veuillez saisir soit un nombre positif, soit au maximum deux chiffres après la décimale',
                    ]),
                    new Length([
                        'max' => 5,
                        'maxMessage' => 'Le nombre ne peut pas dépasser 5 caractéres',
                    ])
                ],
            ])
            ->add('obj100', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un objectif.',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]\d*[.,:]?\d{0,2}$/',
                        'message' => 'Veuillez saisir soit un nombre positif, soit au maximum deux chiffres après la décimale',
                    ]),
                    new Length([
                        'max' => 5,
                        'maxMessage' => 'Le nombre ne peut pas dépasser 5 caractéres',
                    ])
                ],
            ])
            ->add('obj110', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un objectif.',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]\d*[.,:]?\d{0,2}$/',
                        'message' => 'Veuillez saisir soit un nombre positif, soit au maximum deux chiffres après la décimale',
                    ]),
                    new Length([
                        'max' => 5,
                        'maxMessage' => 'Le nombre ne peut pas dépasser 5 caractéres',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objectifs::class,
        ]);
    }
}
