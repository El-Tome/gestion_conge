<?php

namespace App\Service;

use App\Entity\SeuilAlerte;
use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;

class SeuilAlerteService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateSeuilAlerte(float $seuilCritique, string $serviceName = 'default'): void
    {
        // Récupérer le service par son nom
        $service = $this->entityManager->getRepository(Services::class)
            ->findOneBy(['nom' => $serviceName]);
        
        if (!$service) {
            throw new \InvalidArgumentException("Le service '$serviceName' n'existe pas.");
        }

        // Récupérer l'entrée existante ou en créer une nouvelle si nécessaire
        $seuilAlerte = $service->getSeuilAlerte();
        
        if (!$seuilAlerte) {
            // Aucune entrée existante, on en crée une nouvelle
            $seuilAlerte = new SeuilAlerte();
            $seuilAlerte->setService($service);
        }
        
        // Mettre à jour la valeur
        $seuilAlerte->setSeuilCrit($seuilCritique);
        
        // Persister et flusher les changements
        $this->entityManager->persist($seuilAlerte);
        $this->entityManager->flush();
    }
    
    public function getCurrentSeuilAlerte(): ?SeuilAlerte
    {
        // Méthode maintenue pour la compatibilité
        return $this->getSeuilAlerteByService('default');
    }

    public function getSeuilAlerteByService(string $serviceName = 'default'): ?SeuilAlerte
    {
        $service = $this->entityManager->getRepository(Services::class)
            ->findOneBy(['nom' => $serviceName]);
        
        if (!$service) {
            return null;
        }
        
        return $service->getSeuilAlerte();
    }

    public function getAllServices(): array
    {
        return $this->entityManager->getRepository(Services::class)
            ->createQueryBuilder('s')
            ->select('s.id, s.nom as nomService')
            ->getQuery()
            ->getResult();
    }
} 