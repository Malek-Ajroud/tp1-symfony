<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;



final class ArticlesController extends AbstractController
{

#[Route('/test-email', name: 'app_test_email')]
public function testEmail(MailerInterface $mailer): Response
{
    $email = (new Email())
        ->from('noreply@monsite.com')
        ->to('etudiant@exemple.com')
        ->subject('🎉 Test Email depuis Symfony !')
        ->text('Ceci est un email de test envoyé depuis Symfony avec Mailtrap.')
        ->html('<h1>Bravo !</h1><p>Votre configuration Mailtrap fonctionne correctement. 🚀</p>');

    $mailer->send($email);

    $this->addFlash('success', 'Email envoyé avec succès ! Vérifiez votre boîte Mailtrap.');

    return $this->redirectToRoute('app_articles');
}


public function sendEmail(MailerInterface $mailer): Response
{
    $email = (new Email())
        ->from('hello@example.com')
        ->to('you@example.com')
        ->subject('Nouvel Article !')
        ->text('Un nouvel article a été publié sur le blog.')
        ->html('<p>Un nouvel article a été publié sur le blog.</p>');

    $mailer->send($email);
    // ...
}

#[Route('/test-email-twig', name: 'app_test_email_twig')]
public function testEmailTwig(MailerInterface $mailer): Response
{
    $email = (new TemplatedEmail())
        ->from('noreply@monsite.com')
        ->to('etudiant@exemple.com')
        ->subject('Nouvel article publié !')
        ->htmlTemplate('emails/notification.html.twig')
        ->context([
            'subject' => 'Nouvel article publié !',
            'message' => 'Un nouveau contenu vient d\'être ajouté sur le blog.',
        ]);

    $mailer->send($email);

    $this->addFlash('success', 'Email Twig envoyé ! Vérifiez Mailtrap.');

    return $this->redirectToRoute('app_articles');
}
    #[Route('/articles', name: 'app_articles')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }
    

    #[Route('/articles/nouveau', name: 'app_article_nouveau')]
    #[IsGranted('ROLE_USER')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        
        // Création du formulaire
        $form = $this->createForm(ArticleType::class, $article);
        
        // Traitement de la requête
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Affecte l'utilisateur connecté comme auteur de l'article
            $article->setAuteurUser($this->getUser());

            $em->persist($article);
            $em->flush();
            
            // Message flash de confirmation
            $this->addFlash('success', 'Article créé avec succès !');
            
            return $this->redirectToRoute('app_articles');
        }
        
        return $this->render('articles/nouveau.html.twig', [
            'formulaire' => $form,
        ]);
    }

    
    #[Route('/articles/{id}', name: 'app_article_detail', requirements: ['id' => '\d+'])]
    public function detail(Article $article): Response
    {
        return $this->render('articles/detail.html.twig', [
            'article' => $article,
        ]);
    }
}