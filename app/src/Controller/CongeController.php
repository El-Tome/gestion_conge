<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CongeRepository;
use App\Entity\Conge;

class CongeController extends AbstractController
{
    private $congeRepository;

    public function __construct(CongeRepository $congeRepository)
    {
        $this->congeRepository = $congeRepository;
    }

    #[Route('/conge', name: 'conge')]
    public function index(): Response
    {
        // Utiliser la méthode findAll() pour récupérer tous les congés
        $userConges = $this->congeRepository->findAll();

        // Passer les congés à la vue
        return $this->render('conge/index.html.twig', [
            'userConges' => $userConges,
        ]);
    }
   
}
