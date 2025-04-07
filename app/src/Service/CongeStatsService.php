<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository; // Ajustez selon votre structure
use App\Repository\CongeRepository; // Ajustez selon votre structure

class CongeStatsService
{
    private $userRepository;
    private $congeRepository;
    private $seuilAlerteService;

    public function __construct(
        UserRepository $userRepository,
        CongeRepository $congeRepository,
        SeuilAlerteService $seuilAlerteService
    ) {
        $this->userRepository = $userRepository;
        $this->congeRepository = $congeRepository;
        $this->seuilAlerteService = $seuilAlerteService;
    }

    /**
     * Calcule le pourcentage d'utilisateurs actuellement en congé
     */
    public function getPourcentageEnConge(): float
    {
        $totalUsers = $this->userRepository->count([]);
        
        if ($totalUsers === 0) {
            return 0;
        }
        
        $usersEnConge = $this->congeRepository->countActiveConges();
        
        return ($usersEnConge / $totalUsers) * 100;
    }

    /**
     * Vérifie si le pourcentage d'utilisateurs en congé dépasse le seuil critique
     */
    public function isSeuilCritiqueDepasse(): bool
    {
        $pourcentageEnConge = $this->getPourcentageEnConge();
        $seuilAlerte = $this->seuilAlerteService->getCurrentSeuilAlerte();
        
        if (!$seuilAlerte) {
            return false;
        }
        
        return $pourcentageEnConge >= $seuilAlerte->getSeuilCrit();
    }

    /**
     * Vérifie si un utilisateur est actuellement en congé
     */
    public function isUserEnConge(User $user): bool
    {
        $activeConges = $this->congeRepository->findActiveCongesForUser($user);
        return count($activeConges) > 0;
    }

    /**
     * Obtient des informations détaillées sur la situation des congés
     */
    public function getCongeStats(): array
    {
        $totalUsers = $this->userRepository->count([]);
        $usersEnConge = $this->congeRepository->countActiveConges();
        $pourcentage = ($totalUsers > 0) ? ($usersEnConge / $totalUsers) * 100 : 0;
        $seuilAlerte = $this->seuilAlerteService->getCurrentSeuilAlerte();
        $seuilCritique = $seuilAlerte ? $seuilAlerte->getSeuilCrit() : null;
        $isSeuilDepasse = $this->isSeuilCritiqueDepasse();
        
        // Récupérer le nombre de congés en attente
        $pendingConges = $this->congeRepository->countPendingConges();
        
        // Calculer le niveau d'alerte (0: normal, 1: attention, 2: critique)
        $alertLevel = 0;
        if ($seuilCritique) {
            if ($pourcentage >= $seuilCritique) {
                $alertLevel = 2; // Critique
            } elseif ($pourcentage >= $seuilCritique * 0.8) {
                $alertLevel = 1; // Attention (80% du seuil critique)
            }
        }
        
        return [
            'totalUsers' => $totalUsers,
            'usersEnConge' => $usersEnConge,
            'pourcentage' => $pourcentage,
            'seuilCritique' => $seuilCritique,
            'isSeuilDepasse' => $isSeuilDepasse,
            'alertLevel' => $alertLevel,
            'pendingConges' => $pendingConges,
        ];
    }
} 