<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset_password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    // Formulaire de reset
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        // Paramétrage du formulaire
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('username')->getData(),
                $mailer,
                $translator
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    // Page de confirmation
    #[Route('/check_email', name: 'app_forgot_password_check_email')]
    public function checkEmail(): Response
    {

        // Génération d'un fake token si user n'existe pas
        // Cela empêche de savoir si l'adresse mail existe ou non dans la database
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    // Fonction de reset (après que l'utilisateur ait cliqué sur le lien présent dans le mail)
    #[Route('/reset/{token}', name: 'app_forgot_password_reset')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, string $token = null): Response
    {

        if ($token) {
            // Le token est stocké dans la session et chargé dans le navigateur,
            // cela empêche une potentielle fuite par un script JS.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_forgot_password_reset');
        }

        // S'il y a un problème avec le token, on retourne une erreur
        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('Erreur interne.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Si le token est valide, on autorise l'user à changer son pw
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le token est valide une seule fois, on le supprime après utilisation
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encodage du password
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // On clean la session après le reset
            $this->cleanSessionAfterReset();

            // Ajout FlashMsg pour informer du succès + Redirection sur la page de login
            $this->addFlash('success_message', "Votre mot de passe a bien été modifié");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        // On récupère le mail entré par l'utilisateur
        $user = $this->entityManager->getRepository(Users::class)->findOneBy([
            'username' => $emailFormData,
        ]);

        // Si le mail n'existe pas, on redirige vers check_email pour ne pas leaker le fait qu'elle existe ou non
        if (!$user) {
            return $this->redirectToRoute('app_forgot_password_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_forgot_password_check_email');
        }

        // Envoi d'un mail à l'utilisateur
        $senderEmail = $this->getParameter('app.admin_email');
        $senderName = $this->getParameter('app.full_name');
        $senderTitle = $this->getParameter('app.shrink_name');

        $email = (new TemplatedEmail())
            ->from(new Address($senderEmail, $senderName))
            ->to($user->getUsername())
            ->subject($senderTitle . ' - Mot de passe oublié')
            ->htmlTemplate('reset_password/template_email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);

        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_forgot_password_check_email');
    }
}
