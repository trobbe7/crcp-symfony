<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ResultatsRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChartsController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // Dashboard Chart

    #[Route('/charts/dashboard', name: 'app_api_dashboard_chart', methods: ['GET'])]
    public function dashboardChart(ResultatsRepository $resultatsRepository): Response
    {

        $uid = $this->security->getUser();
        $uid = $uid->getId();
        $repo = $resultatsRepository->retrieveGraphActualMonth($uid);

        $data = array(
            'chartDate' => array(),
            'chartMoyenne' => array(),
            'chartTel' => array(),
            'chartMail' => array(),
            'chartDEC' => array(),
        );

        $totalTel = 0;
        $totalMail = 0;
        $totalDec = 0;
        $totalTraitements = 0;

        foreach ($repo as $row) {
            $data['chartDate'][] = $row->getCreatedAt()->format('d/m');
            $data['chartMoyenne'][] = sprintf("%4.1f", ($row->getTraitements() / $row->getFullTime()));
            $totalTel = $totalTel + $row->getTel();
            $totalMail = $totalMail + $row->getMail();
            $totalDec = $totalDec + $row->getCorrespondances();
            $totalTraitements = $totalTraitements + $row->getTraitements();
        }

        $data['chartTel'][] = sprintf("%4.1f", (($totalTel * 100) / $totalTraitements));
        $data['chartMail'][] = sprintf("%4.1f", (($totalMail * 100) / $totalTraitements));
        $data['chartDEC'][] = sprintf("%4.1f", (($totalDec * 100) / $totalTraitements));

        return $this->json($data);
    }

    // Historique Charts

    #[Route('/charts/historique/{resultat}', name: 'app_api_detail_chart', methods: ['GET'])]
    public function historiqueChart(ResultatsRepository $resultatsRepository, int $resultat = null): Response
    {

        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupère les infos concernant le résultat en question
        $repo = $resultatsRepository->findOneBy(['id' => $resultat]);

        // Si l'utilisateur qui tente d'accéder au résultat n'est pas propriétaire, on Throw une Exception
        try {
            if ($uid != $repo->getUid()) {
                throw new Exception("Accès interdit", 403);
            }
        } catch (Exception $e) {
            return new JsonResponse(["error" => true], $e->getCode());
        }

        $data = array(
            'chartTel',
            'chartMail',
            'chartDEC',
        );

        $data['chartTel'] = $repo->getTel();
        $data['chartMail'] = $repo->getMail();
        $data['chartDEC'] = $repo->getCorrespondances();

        return $this->json($data);
    }

}
