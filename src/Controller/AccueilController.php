<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ArticleRepository;

final class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(RequestStack $requestStack, ArticleRepository $articleRepository): Response
    {
        $session = $requestStack->getSession();
        
        $nbVisites = $session->get('nb_visites', 0);
        $session->set('nb_visites', $nbVisites + 1);

        $derniersArticles = $articleRepository->findLastPublished(3);
    
        return $this->render('accueil/index.html.twig', [
            'derniersArticles' => $derniersArticles,
            'nbVisites' => $nbVisites,
        ]);
    }
    
    #[Route('/profil/{id}', name: 'app_profil', requirements: ['id' => '\d+'], defaults: ['id' => 1])]
    public function profil(int $id): Response
    {
        return new Response("<h1>Profil de l'utilisateur n°$id</h1>");
    }
    #[Route('/bonjour/{prenom}', name: 'app_bonjour')]
    public function bonjour(string $prenom): Response
    {
        return new Response("<h1>Bonjour $prenom ! Bienvenue sur Symfony 7.4</h1>");
    } 
    #[Route('/articles/{id}/modifier', name: 'app_article_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();
        if ($article->getAuteurUser() !== null && $currentUser !== $article->getAuteurUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet article.');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); 

            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('app_article_detail', ['id' => $article->getId()]);
        }

        return $this->render('articles/modifier.html.twig', [
            'formulaire' => $form,
            'article' => $article,
        ]);
    }
    #[Route('/articles/{id}/supprimer', name: 'app_article_supprimer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprimer(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('supprimer_' . $article->getId(), $request->request->get('_token'))) {
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'Article supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide. Suppression annulée.');
        }

        return $this->redirectToRoute('app_articles');
    }
    
}
