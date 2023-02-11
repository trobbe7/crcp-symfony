<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationForm;
use App\Security\LogInAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LogInAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = new Users();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // ajout de la date de création du compte
            $currentDate = date('Y-m-d H:i:s');
            $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $currentDate);
            $user->setCreatedAt($createdAt);

            // ajout du ROLE_USER lors de l'inscription
            $roles = array('ROLE_USER');
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();

            // Actions futures

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
