<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', EmailType::class, [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Email',
                'style' => 'margin-bottom: 10px;',
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
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
