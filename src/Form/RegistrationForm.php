<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Mail Pro - xxx@exemple.fr',
                    'style' => 'margin-bottom: 0px;',
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse mail',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_.+-]+@(?:oney\.fr|oney\.com|partner\.oney\.fr|oneytrust\.com)$/',
                        'message' => 'Votre adresse mail ne fait pas partie des domaines autorisés',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => true,
                'type' => PasswordType::class,
                'first_options'  => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control',
                        'placeholder' => 'Mot de passe',
                        'style' => 'margin-bottom: 0px;',
                    ],
                    'label' => false,
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control',
                        'placeholder' => 'Mot de passe - Confirmation'
                    ],
                    'label' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
