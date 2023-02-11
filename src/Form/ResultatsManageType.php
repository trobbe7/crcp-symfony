<?php

namespace App\Form;

use App\Entity\Resultats;
use App\Repository\ResultatsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class ResultatsManageType extends AbstractType
{

    private Security $security;
    private ResultatsRepository $resultatsRepository;

    public function __construct(Security $security, ResultatsRepository $resultatsRepository)
    {
        $this->security = $security;
        $this->resultatsRepository = $resultatsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // Récupération de l'uid
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupération du dernier traitement
        $repo = $this->resultatsRepository->getLastTraitement($uid);

        $builder
            ->add('tel', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $repo->getTel(),
                    'value' => $repo->getTel(),
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
                    'placeholder' => $repo->getMail(),
                    'value' => $repo->getMail(),
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
                    'placeholder' => $repo->getCorrespondances(),
                    'value' => $repo->getCorrespondances(),
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
                    'value' => $repo->getFullTime(),
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resultats::class,
        ]);
    }
}
