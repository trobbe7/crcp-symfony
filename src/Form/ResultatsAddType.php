<?php

namespace App\Form;

use App\Entity\Resultats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class ResultatsAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tel', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un résultat',
                    ]),
                    new Type([
                        'integer',
                        'message' => 'Veuillez saisir un nombre entier',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]\d*$/',
                        'message' => 'Veuillez saisir un nombre positif',
                    ]),
                    new Length([
                        'max' => 4,
                        'maxMessage' => 'Le résultat ne peut pas dépasser 4 chiffres',
                    ])
                ],
            ])
            ->add('mail', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un résultat',
                    ]),
                    new Type([
                        'integer',
                        'message' => 'Veuillez saisir un nombre entier',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]\d*$/',
                        'message' => 'Veuillez saisir un nombre positif',
                    ]),
                    new Length([
                        'max' => 4,
                        'maxMessage' => 'Le résultat ne peut pas dépasser 4 chiffres',
                    ])
                ],
            ])
            ->add('correspondances', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un résultat',
                    ]),
                    new Type([
                        'integer',
                        'message' => 'Veuillez saisir un nombre entier',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]\d*$/',
                        'message' => 'Veuillez saisir un nombre positif',
                    ]),
                    new Length([
                        'max' => 4,
                        'maxMessage' => 'Le résultat ne peut pas dépasser 4 chiffres',
                    ])
                ],
            ])
            ->add('full_time', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Visible sur RHPi en fin de journée',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un résultat',
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
            ->add('commentaire', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ce champ est facultatif',
                    'rows' => '1',
                ],
                'label' => false,
                'required' => false,
            ]);     
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resultats::class,
            'sanitize_html' => true,
        ]);
    }
}
