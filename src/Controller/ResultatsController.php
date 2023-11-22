<?php

namespace App\Controller;

use App\Entity\Resultats;
use App\Form\ResultatsAddType;
use App\Form\ResultatsManageType;
use App\Repository\ResultatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatsController extends AbstractController
{

    private Security $security;
    private ResultatsRepository $resultatsRepository;

    public function __construct(Security $security, ResultatsRepository $resultatsRepository)
    {
        $this->security = $security;
        $this->resultatsRepository = $resultatsRepository;
    }

    #[Route('/resultats/add', name: 'app_resultats_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        // Récupération de l'uid
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Si User a déjà un résultat aujourd'hui, on l'informe
        $repo = $this->resultatsRepository->countTodayTraitements($uid);

        if ($repo > 0) {
            $this->addFlash('warning_message', "Un résultat existe déjà pour aujourd'hui");
        }

        // Paramétrage du formulaire
        $resultat = new Resultats();
        $form = $this->createForm(ResultatsAddType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération des inputs utilisateur sur le formulaire
            $data = $form->getData();

            // Application de l'uid
            $resultat->setUid($uid);

            // Ajout des données
            $resultat->setTel($data->getTel());
            $resultat->setMail($data->getMail());
            $resultat->setCorrespondances($data->getCorrespondances());
            $resultat->setFullTime(str_replace([':', ','], '.', $data->getFullTime())); // formatage de la donnée (: && , -> .)

            // Addition de tout les trt pour alimenter le total traitements
            $resultat->setTraitements($data->getTel() + $data->getMail() + $data->getCorrespondances());

            // Transformation du full_time en time_minutes
            $resultat->setTimeMinutes($data->getFullTime() * 60);

            // Si un commentaire spécifique est renseigné par user, on le récupère
            if($data->getCommentaire()) {
                $resultat->setCommentaire($data->getCommentaire());
            }

            // Ajout de la date de la création du résultat
            $currentDate = date('Y-m-d H:i:s');
            $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $currentDate);
            $resultat->setCreatedAt($createdAt);

            // Insert Doctrine
            $entityManager->persist($resultat);
            $entityManager->flush();

            // Ajout FlashMsg pour informer du succès + Redirection sur le dashboard
            $this->addFlash('success_message', "Le resultat a bien été crée");
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('resultats/add.html.twig', [
            'resultat_form' => $form->createView(),
        ]);
    }

    #[Route('/resultats/manage', name: 'app_resultats_manage')]
    public function manage(Request $request, EntityManagerInterface $entityManager): Response
    {

        // Récupération de l'uid
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupération du dernier traitement
        $repo = $this->resultatsRepository->getLastTraitement($uid);

        // Si aucun traitement est existant (= nouvel utilisateur || modification impossible)
        if (!$repo) {
            $this->addFlash('error_message', "Vous devez d'abord créer un résultat avant de pouvoir le modifier");
            return $this->redirectToRoute('app_dashboard');
        }
        // Si le mois est clôturé (= redirection dashboard || modification impossible)
        elseif ((date('Y-m', strtotime($repo->getCreatedAt()->format('Y-m')))) != (date('Y-m'))) {
            $this->addFlash('error_message', "Le mois de votre résultat est clôturé. Il est donc impossible de le modifier");
            return $this->redirectToRoute('app_dashboard');
        }
        // Si le résultat ne date pas d'aujourd'hui, on informe user
        elseif ((date('Y-m-d', strtotime($repo->getCreatedAt()->format('Y-m-d')))) != (date('Y-m-d'))) {
            $this->addFlash('warning_message', "Le résultat ne date pas d'aujourd'hui");
        }

        // Paramétrage du formulaire
        $form = $this->createForm(ResultatsManageType::class, $repo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération des inputs utilisateur sur le formulaire
            $data = $form->getData();

            // Application de l'uid 
            $repo->setUid($uid);

            // Ajout des données
            $repo->setTel($data->getTel());
            $repo->setMail($data->getMail());
            $repo->setCorrespondances($data->getCorrespondances());
            $repo->setFullTime(str_replace([':', ','], '.', $data->getFullTime())); // formatage de la donnée (: && , -> .)

            // Addition de tout les trt pour alimenter le total traitements
            $repo->setTraitements($data->getTel() + $data->getMail() + $data->getCorrespondances());

            // Transformation du full_time en time_minutes
            $repo->setTimeMinutes($data->getFullTime() * 60);

            // Si un commentaire spécifique est renseigné par user, on le récupère
            if($data->getCommentaire()) {
                $repo->setCommentaire($data->getCommentaire());
            }

            // Update Doctrine
            $entityManager->persist($repo);
            $entityManager->flush();

            // Ajout FlashMsg pour informer du succès + Redirection sur le dashboard
            $this->addFlash('success_message', "Le resultat a bien été mis à jour");
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('resultats/manage.html.twig', [
            'resultat_form' => $form->createView(),
            'resultat_date' => $repo->getCreatedAt(),
        ]);
    }
}