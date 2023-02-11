<?php

// Use of : src\Twig\AppExtension.php

namespace App\Controller;

use App\Repository\ResultatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/historique', name: 'app_historique')]
    public function index(ResultatsRepository $resultatsRepository): Response
    {

        // Récupère le le userID
        $uid = $this->security->getUser();
        $uid = $uid->getId();

        // Récupère tout les résultats de l'utilisateur
        $repo = $resultatsRepository->getAllByID($uid);

        return $this->render('historique/index.html.twig', [
            'historique' => $repo,
        ]);
    }
}
