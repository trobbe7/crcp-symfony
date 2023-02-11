<?php

namespace App\Controller;

use App\Entity\Objectifs;
use App\Form\ObjectifsAddType;
use App\Form\ObjectifsManageType;
use App\Repository\ObjectifsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ObjectifsController extends AbstractController
{

    private Security $security;
    private ObjectifsRepository $objectifsRepository;

    public function __construct(Security $security, ObjectifsRepository $objectifsRepository)
    {
        $this->security = $security;
        $this->objectifsRepository = $objectifsRepository;
    }

    #[Route('/objectifs/add', name: 'app_objectifs_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        // Récupération de l'uid
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Si User a déjà un objectif ce mois-ci, redirection vers dashboard, double obj/mois impossible
        $repo = $this->objectifsRepository->countMonthObjectif($uid);

        if ($repo > 0) {
            $this->addFlash('error_message', "Un objectif existe déjà pour ce mois-ci");
            return $this->redirectToRoute('app_dashboard');
        }

        // Paramétrage du formulaire
        $objectif = new Objectifs();
        $form = $this->createForm(ObjectifsAddType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération des inputs utilisateur sur le formulaire
            $data = $form->getData();

            // Application de l'uid
            $objectif->setUid($uid);

            // Ajout des données + formatage de la donnée (: && , -> .)
            $objectif->setObj90(str_replace([':', ','], '.', $data->getObj90()));
            $objectif->setObj100(str_replace([':', ','], '.', $data->getObj100()));
            $objectif->setObj110(str_replace([':', ','], '.', $data->getObj110()));

            // Ajout de la date de la création du résultat
            $currentDate = date('Y-m-d H:i:s');
            $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $currentDate);
            $objectif->setCreatedAt($createdAt);

            // Insert Doctrine
            $entityManager->persist($objectif);
            $entityManager->flush();

            // Ajout FlashMsg pour informer du succès + Redirection sur le dashboard
            $this->addFlash('success_message', "L'objectif a bien été crée");
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('objectifs/add.html.twig', [
            'objectif_form' => $form->createView(),
        ]);
    }

    #[Route('/objectifs/manage', name: 'app_objectifs_manage')]
    public function manage(Request $request, EntityManagerInterface $entityManager): Response
    {

        // Récupération de l'uid
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupération du dernier objectif
        $repo = $this->objectifsRepository->getLastObjectif($uid);

        // Si aucun objectif est existant (= nouvel utilisateur || modification impossible)
        if (!$repo) {
            $this->addFlash('error_message', "Vous devez d'abord créer un objectif avant de pouvoir le modifier");
            return $this->redirectToRoute('app_dashboard');
        }
        // Si le mois est clôturé (= redirection dashboard || modification impossible)
        elseif ((date('Y-m', strtotime($repo->getCreatedAt()->format('Y-m')))) != (date('Y-m'))) {
            $this->addFlash('error_message', "Le mois de votre objectif est clôturé. Il est donc impossible de le modifier");
            return $this->redirectToRoute('app_dashboard');
        }

        // Paramétrage du formulaire
        $form = $this->createForm(ObjectifsManageType::class, $repo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération des inputs utilisateur sur le formulaire
            $data = $form->getData();

            // Application de l'uid 
            $repo->setUid($uid);

            // Ajout des données + formatage de la donnée (: && , -> .)
            $repo->setObj90(str_replace([':', ','], '.', $data->getObj90()));
            $repo->setObj100(str_replace([':', ','], '.', $data->getObj100()));
            $repo->setObj110(str_replace([':', ','], '.', $data->getObj110()));

            // Update Doctrine
            $entityManager->persist($repo);
            $entityManager->flush();

            // Ajout FlashMsg pour informer du succès + Redirection sur le dashboard
            $this->addFlash('success_message', "L'objectif a bien été mis à jour");
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('objectifs/manage.html.twig', [
            'objectif_form' => $form->createView(),
        ]);
    }
}
