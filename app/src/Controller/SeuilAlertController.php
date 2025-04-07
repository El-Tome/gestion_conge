<?php

namespace App\Controller;

use App\Entity\Services;
use App\Form\SeuilAlerteType;
use App\Service\SeuilAlerteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeuilAlertController extends AbstractController
{
    #[Route('/seuil/alert', name: 'app_seuil_alert')]
    public function index(Request $request, SeuilAlerteService $seuilAlerteService): Response
    {
        // Récupérer le service demandé depuis le paramètre de requête ou utiliser 'default'
        $serviceName = $request->query->get('service', 'default');
        
        // Récupérer le seuil actuel pour ce service
        $currentSeuil = $seuilAlerteService->getSeuilAlerteByService($serviceName);
        
        // Préremplir le formulaire avec les valeurs actuelles
        $defaultData = [
            'seuilCritique' => $currentSeuil ? $currentSeuil->getSeuilCrit() : null,
            'nomService' => $serviceName
        ];
        
        // Création du formulaire
        $form = $this->createForm(SeuilAlerteType::class, $defaultData);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Récupérer le nom du service à partir de l'entité Service sélectionnée
            $selectedService = $form->get('nomService')->getData();
            $serviceName = $selectedService->getNom();
            
            // Déléguer la logique de mise à jour au service
            $seuilAlerteService->updateSeuilAlerte($data['seuilCritique'], $serviceName);
            
            $this->addFlash('success', 'Le seuil critique pour le service ' . $serviceName . ' a été mis à jour avec succès!');
            
            return $this->redirectToRoute('app_seuil_alert', ['service' => $serviceName]);
        }
        
        // Récupérer la liste de tous les services existants
        $allServices = $seuilAlerteService->getAllServices();
        
        return $this->render('seuil_alert/index.html.twig', [
            'form' => $form->createView(),
            'currentSeuil' => $currentSeuil,
            'serviceName' => $serviceName,
            'allServices' => $allServices
        ]);
    }

    // Ajouter éventuellement une route pour lister tous les services configurés
    #[Route('/seuil/alert/services', name: 'app_seuil_alert_services')]
    public function listServices(SeuilAlerteService $seuilAlerteService): Response
    {
        $services = $seuilAlerteService->getAllServices();
        
        return $this->render('seuil_alert/services.html.twig', [
            'services' => $services,
        ]);
    }
}
