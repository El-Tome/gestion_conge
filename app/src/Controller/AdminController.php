<?php

namespace App\Controller;

use App\Entity\Services;
use App\Service\CongeStatsService;
use App\Service\SeuilAlerteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin_dashboard', name: 'admin_dashboard')]
    public function dashboard(
        CongeStatsService $congeStatsService,
        SeuilAlerteService $seuilAlerteService,
        EntityManagerInterface $entityManager
    )
    {
        // Récupérer les statistiques des congés
        $congeStats = $congeStatsService->getCongeStats();

        // Récupérer les conges par service
        $services = $entityManager->getRepository(Services::class)->findAll();
        $serviceStats = [];
        
        foreach ($services as $service) {
            $serviceNom = $service->getNom();
            $users = $service->getUsers();
            $totalUsersInService = count($users);
            
            if ($totalUsersInService > 0) {
                $usersEnConge = 0;
                foreach ($users as $user) {
                    if ($congeStatsService->isUserEnConge($user)) {
                        $usersEnConge++;
                    }
                }
                
                $pourcentage = ($usersEnConge / $totalUsersInService) * 100;
                $seuilAlerte = $service->getSeuilAlerte();
                $seuilCritique = $seuilAlerte ? $seuilAlerte->getSeuilCrit() : null;
                $isSeuilDepasse = $seuilCritique && $pourcentage >= $seuilCritique;
                
                // Calculer le niveau d'alerte (0: normal, 1: attention, 2: critique)
                $alertLevel = 0;
                if ($seuilCritique) {
                    if ($pourcentage >= $seuilCritique) {
                        $alertLevel = 2; // Critique
                    } elseif ($pourcentage >= $seuilCritique * 0.8) {
                        $alertLevel = 1; // Attention (80% du seuil critique)
                    }
                }
                
                $serviceStats[$serviceNom] = [
                    'totalUsers' => $totalUsersInService,
                    'usersEnConge' => $usersEnConge,
                    'pourcentage' => $pourcentage,
                    'seuilCritique' => $seuilCritique,
                    'isSeuilDepasse' => $isSeuilDepasse,
                    'alertLevel' => $alertLevel
                ];
            }
        }

        return $this->render('admin/dashboard.html.twig', [
            'congeStats' => $congeStats,
            'serviceStats' => $serviceStats,
        ]);
    }
}