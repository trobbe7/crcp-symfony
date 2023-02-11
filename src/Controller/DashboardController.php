<?php

namespace App\Controller;

use App\Repository\ObjectifsRepository;
use App\Repository\ResultatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ResultatsRepository $resultatsRepository, ObjectifsRepository $objectifsRepository): Response
    {

        // Retourne le nombre de jours restants
        $timestamp = date("y-m-d");
        $timestamp = strtotime($timestamp);
        $remainingDays = (int)date('t', $timestamp) - (int)date('j', $timestamp);

        // Récupère le le userID
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Retourne les traitements && la moyenne -> $uid
        $temps = $resultatsRepository->getActualMonthWorkingTime($uid);
        $trt = $resultatsRepository->getActualMonthTraitements($uid);

        // Si l'une des valeurs = 0, la division n'est pas possible
        if ($temps > 0 && $trt > 0) {
            $moyenne = sprintf("%4.1f", ($trt / $temps));
        } else {
            $moyenne = 0;
        }

        // Récupération du temps en minute pour l'affichage en heure (d_FullTime sur Twig)
        $workingTime = $resultatsRepository->getActualMonthWorkingMinutes($uid);

        // Récupération des infos sur l'objectif, si pas d'obj paramétré : retourne "-"
        $objectif = $objectifsRepository->getActualMonthObjectif($uid);
        if (!$objectif) {
            $obj90 = "-";
            $obj100 = "-";
            $obj110 = "-";
        } else {
            $obj90 = sprintf("%4.1f", $objectif->getObj90());
            $obj100 = sprintf("%4.1f", $objectif->getObj100());
            $obj110 = sprintf("%4.1f", $objectif->getObj110());
        }

        return $this->render('dashboard/index.html.twig', [
            'moyenne' => $moyenne,
            'traitements' => $trt,
            'remainingDays' => $remainingDays,
            'workingTime' => $workingTime,
            'obj90' => $obj90,
            'obj100' => $obj100,
            'obj110' => $obj110,
        ]);
    }
}
