#[Route('/taches', name: 'app_taches')]
    public function index(Repository $tacheRepository): Response
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
    #[Route('/taches/{id}', name: 'app_tache_detail', requirements: ['id' => '\d+'])]
    public function detail(Tache $tache): Response
    {
        return $this->render('taches/detail.html.twig', [
            'tache' => $tache,
        ]);
    }
