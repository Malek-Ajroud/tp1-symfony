<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TacheController extends AbstractController
{
    #[Route('/taches', name: 'app_taches')]
    public function index(TacheRepository $tacheRepository): Response
    {
        $taches = $tacheRepository->findAll();

        return $this->render('taches/index.html.twig', [
            'taches' => $taches,
        ]);
    }

    #[Route('/taches/nouvelle', name: 'app_tache_nouvelle')]
    public function nouvelle(EntityManagerInterface $em): Response
    {
        $tache = new Tache();
        $tache->setTitre('Ma première tâche');
        $tache->setDescription('Ceci est la description de ma première tâche créée avec Doctrine.');
        $tache->setDateCreation(new \DateTime());
        $tache->setTerminee(false);

        $em->persist($tache);
        $em->flush();

        return new Response("Tâche créée avec l'id : " . $tache->getId());
    }

    #[Route('/taches/{id}', name: 'app_tache_detail', requirements: ['id' => '\\d+'])]
    public function detail(Tache $tache): Response
    {
        return $this->render('taches/detail.html.twig', [
            'tache' => $tache,
        ]);
    }

    #[Route('/taches/{id}/terminer', name: 'app_tache_terminer', requirements: ['id' => '\\d+']) ]
    public function terminer(Tache $tache, EntityManagerInterface $em): Response
    {
        if ($tache->isTerminee()) {
            return new Response("La tâche est déjà terminée.");
        }

        $tache->setTerminee(true);
        $em->flush();

        return new Response("Tâche avec l'id " . $tache->getId() . " marquée comme terminée.");
    }
}
