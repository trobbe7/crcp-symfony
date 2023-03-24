<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Paramétrage du formulaire
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Ajout de la date de création du compte
            $currentDate = date('Y-m-d H:i:s');
            $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $currentDate);
            $user->setCreatedAt($createdAt);

            // Ajout du ROLE_USER lors de l'inscription
            $roles = array('ROLE_USER');
            $user->setRoles($roles);

            // Insert Doctrine
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi d'un mail à l'utilisateur
            $senderEmail = $this->getParameter('app.admin_email');
            $senderName = $this->getParameter('app.full_name');
            $senderTitle = $this->getParameter('app.shrink_name');

            $this->emailVerifier->sendEmailConfirmation(
                'app_register_verify',
                $user,
                (new TemplatedEmail())
                    ->from(new Address($senderEmail, $senderName))
                    ->to($user->getUsername())
                    ->subject($senderTitle . ' - Activation de votre compte')
                    ->htmlTemplate('registration/template_email.html.twig')
            );

            // On redirige l'utilisateur sur la page de check mail si le formulaire est correct
            return $this->redirectToRoute('app_register_check_email');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/check_email', name: 'app_register_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('registration/check_email.html.twig');
    }

    #[Route('/register/verify', name: 'app_register_verify')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UsersRepository $usersRepository): Response
    {

        // Si l'id utilisateur n'existe pas (= lien altéré), on redirige vers la homepage d'inscription
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $usersRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // Si le lien reçu dans le mail est valide, on définit User::isVerified = true & Insert Doctrine
        // au cas contraire, on retourne une erreur
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // Ajout FlashMsg pour informer du succès + Redirection sur la login page
        $this->addFlash('success_message', "Votre compte a bien été activé");
        return $this->redirectToRoute('app_login');
    }

}
