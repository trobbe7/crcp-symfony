<?php

// Use of : src\Twig\AppExtension.php

namespace App\Controller;

use App\Repository\ResultatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/historique')]
class HistoriqueController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /*
    Historique Simplifié
    */

    #[Route('', name: 'app_historique')]
    public function index(ResultatsRepository $resultatsRepository): Response
    {

        // Récupère le userID
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupère tout les résultats de l'utilisateur
        $repo = $resultatsRepository->getAllByID($uid);

        // Si aucun traitement est existant (= nouvel utilisateur || consultation impossible)
        if (!$repo) {
            $this->addFlash('error_message', "Vous devez d'abord créer un résultat avant de pouvoir consulter l'historique");
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('historique/index.html.twig', [
            'historique' => $repo,
        ]);
    }

    /*
    Historique Détaillé
    */

    // Liste des années/mois
    #[Route('/detail/list', name: 'app_historique_detail_list')]
    public function list(ResultatsRepository $resultatsRepository): Response
    {

        // Récupère le userID
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupère tout les résultats de l'utilisateur
        $repo = $resultatsRepository->getAllByID($uid);

        // Si aucun traitement est existant (= nouvel utilisateur || consultation impossible)
        if (!$repo) {
            $this->addFlash('error_message', "Vous devez d'abord créer un résultat avant de pouvoir consulter l'historique");
            return $this->redirectToRoute('app_dashboard');
        }        

        return $this->render('historique/list.html.twig', [
            'historique' => $repo,
        ]);

    }

    // Liste des résultats dans années/mois
    #[Route('/detail/list/{year}/{month}', name: 'app_historique_detail_list_ym')]
    public function listYM(ResultatsRepository $resultatsRepository, int $year = null, int $month = null): Response
    {

        // Récupère le userID
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupère les résultats pour l'année/mois
        $repo = $resultatsRepository->getFromYM($year, $month, $uid);

        // Si aucun résultat n'existe pour la période, on redirige au dashboard
        if (!$repo) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('historique/list_ym.html.twig', [
            'historique' => $repo,
            'input_date' => $year . '-' . $month,
        ]);

    }

    // Détail du résultat
    #[Route('/detail/result/{resultat}', name: 'app_historique_detail_result')]
    public function detail(ResultatsRepository $resultatsRepository, int $resultat = null): Response
    {

        // Récupère le userID
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupère les infos concernant le résultat en question
        $repo = $resultatsRepository->findOneBy(['id' => $resultat]);

        // Si le résultat n'existe pas, on redirige au dashboard
        if (!$repo) {
            return $this->redirectToRoute('app_dashboard');
        }

        // Si le résultat n'appartient pas à User qui tente d'y accéder, on redirige au dashboard
        if ($uid != $repo->getUid()) {
            return $this->redirectToRoute('app_dashboard');
        }

        // Si le résultat date < 2022-07-01, informe User que les graphs ne sont pas dispos
        if ((date('Y-m-d', strtotime($repo->getCreatedAt()->format('Y-m-d')))) < (date('2022-07-01'))) {
            $this->addFlash('information_message', "Les résultats datant d'avant le 01/07/2022 ne disposent pas de graphiques suite à une réorganisation de la base de données");
        }

        return $this->render('historique/detail.html.twig', [
            'resultat' => $repo,
        ]);
    }
    
}
