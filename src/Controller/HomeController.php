<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;



class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Article::class);
        $articles = $repository->findAll();

           
        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'La liste d\'articles',
            'articles' => $articles,
            

        ]);
    }


    #[Route('/afficherArticle/{id}', name: 'afficherArticle')]
    public function affichage($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        
         $repository = $entityManager->getRepository(Article::class);
        $articles = $repository->find($id);
           
        

        return $this->render('home/affichage.html.twig', [
        
            'articles' => $articles,
            

        ]);
    }

     #[Route('/article/{id}/voter', name: 'article_vote', methods:"POST")]
    public function articleVote(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        
         

         $direction = $request->request->get('direction');

         if($direction == 'up'){
            $article->setVotes($article->getVotes() +1);
         }elseif($direction == "down"){
            $article->setVotes($article->getVotes() -1);
         }

         $entityManager->flush();

         return $this->redirectToRoute('app_home');


        

           
        

        
    }




    #[Route('/new', name: 'newArticle')]
    public function new (Request $request, EntityManagerInterface $entityManager): Response
    {
     $article = new Article(); 
        $category= new Category();
        $form = $this->createForm(ArticleType::class, $article);
        $form ->handleRequest($request); 

        if($form ->isSubmitted() && $form->isValid()){
            $article = $form->getData(); 

          

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('final');
        }

        return $this->render('home/new.html.twig', [
            'controller_name' => 'HomeController',
            'article' => $article,
            'form' =>  $form->createView()

        ]);   
    }

    #[Route('/final', name: 'final')]
    public function final (Request $request, EntityManagerInterface $entityManager): Response
    {
                

        return $this->render('home/final.html.twig', [
            'controller_name' => 'Article créer dans la db',
            
            

        ]);
    }
}
