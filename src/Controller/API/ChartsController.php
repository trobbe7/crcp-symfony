<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ResultatsRepository;

class ChartsController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/charts/area', name: 'app_api_chartarea', methods: ['GET'])]
    public function areaChart(ResultatsRepository $resultatsRepository): Response
    {

        $data = array(
            'chartDate' => array(),
            'chartMoyenne' => array(),
        );

        $uid = $this->security->getUser();
        $uid = $uid->getId();
        $repo = $resultatsRepository->retrieveGraphActualMonth($uid);

        foreach ($repo as $row) {
            $data['chartDate'][] = $row->getCreatedAt()->format('d/m');
            $data['chartMoyenne'][] = sprintf("%4.1f", ($row->getTraitements() / $row->getFullTime()));
        }

        return $this->json($data);
    }

    #[Route('/charts/pie', name: 'app_api_piechart', methods: ['GET'])]
    public function pieChart(ResultatsRepository $resultatsRepository): Response
    {

        $uid = $this->security->getUser();
        $uid = $uid->getId();
        $repo = $resultatsRepository->retrieveGraphActualMonth($uid);

        $totalTel = 0;
        $totalMail = 0;
        $totalDec = 0;
        $totalTraitements = 0;

        foreach ($repo as $row) {
            $totalTel = $totalTel + $row->getTel();
            $totalMail = $totalMail + $row->getMail();
            $totalDec = $totalDec + $row->getCorrespondances();
            $totalTraitements = $totalTraitements + $row->getTraitements();
        }

        $data = array(
            'chartTel' => array(),
            'chartMail' => array(),
            'chartDEC' => array(),
        );

        $data['chartTel'][] = sprintf("%4.1f", (($totalTel * 100) / $totalTraitements));
        $data['chartMail'][] = sprintf("%4.1f", (($totalMail * 100) / $totalTraitements));
        $data['chartDEC'][] = sprintf("%4.1f", (($totalDec * 100) / $totalTraitements));

        return $this->json($data);
    }
}
