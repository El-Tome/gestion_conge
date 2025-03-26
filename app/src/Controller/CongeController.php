<?php

namespace App\Controller;

use App\Form\CongeType;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/conge/admin', name: 'conge_list')]
    public function index(): Response
    {
        // Utiliser la méthode findAll() pour récupérer tous les congés
        $userConges = $this->congeRepository->findAll();

        // Passer les congés à la vue
        return $this->render('conge/index.html.twig', [
            'userConges' => $userConges,
        ]);
    }


    #[Route('/conge', name: 'conge_show')]
    public function conge()
    {
        // get all conge from user
        $conges = $this->congeRepository->findBy(['user' => $this->getUser()]);

        // get number of conge by type of conge
        $nbConge = [];
        foreach ($conges as $conge) {
            $typeConge = $conge->getType();
            if (!isset($nbConge[$typeConge])) {
                $nbConge[$typeConge] = 0;
            }
            $nbConge[$typeConge]++;
        }

        return $this->render('conge/show.html.twig', [
            'conges' => $conges,
            'nbConge' => $nbConge,
        ]);
    }

    #[Route('/conge/new', name: 'conge_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conge = new Conge();
        $form = $this->createForm(CongeType::class, $conge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conge->setStatut('en attente');
            $conge->setUser($this->getUser());

            $entityManager->persist($conge);
            $entityManager->flush();

            return $this->redirectToRoute('conge_show');
        }

        return $this->render('conge/new.html.twig', [
            'congeForm' => $form->createView(),
        ]);
    }
    
    #[Route('/conge/{id}/edit', name: 'conge_edit')]
    public function edit(Request $request, Conge $conge, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CongeType::class, $conge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('conge');
        }

        return $this->render('conge/edit.html.twig', [
            'congeForm' => $form->createView(),
        ]);
    }

    #[Route('/conge/{id}/delete', name: 'conge_delete')]
    public function delete(Conge $conge, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($conge);
        $entityManager->flush();

        return $this->redirectToRoute('conge');
    }
    
    #[Route('/conge/{id}/accept', name: 'conge_accept')]
    public function accept(Conge $conge, EntityManagerInterface $entityManager): Response
    {
        $conge->setStatut('accepté');
        $entityManager->flush();
        return $this->redirectToRoute('conge_list');
    }

    #[Route('/conge/{id}/refuse', name: 'conge_refuse')]
    public function refuse(Conge $conge, EntityManagerInterface $entityManager): Response
    {
        $conge->setStatut('refusé');
        $entityManager->flush();
        return $this->redirectToRoute('conge_list');
    }
   
}
