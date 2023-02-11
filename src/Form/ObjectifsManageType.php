<?php

namespace App\Form;

use App\Entity\Objectifs;
use App\Repository\ObjectifsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class ObjectifsManageType extends AbstractType
{

    private Security $security;
    private ObjectifsRepository $objectifsRepository;

    public function __construct(Security $security, ObjectifsRepository $objectifsRepository)
    {
        $this->security = $security;
        $this->objectifsRepository = $objectifsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // Récupération de l'uid
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupération du dernier objectif
        $repo = $this->objectifsRepository->getLastObjectif($uid);

        $builder
            ->add('obj90', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $repo->getObj90(),
                    'value' => $repo->getObj90(),
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
                    'placeholder' => $repo->getObj100(),
                    'value' => $repo->getObj100(),
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
                    'placeholder' => $repo->getObj110(),
                    'value' => $repo->getObj110(),
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
