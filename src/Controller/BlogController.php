<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog', name: 'blog_',)]
class BlogController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function homeAction(): Response
    {
        return $this->render('blog/blog.html.twig');
    }

    #[Route('/list/{page}', name: 'listAction',
        requirements: ['page' => '\d+'],
        defaults: ['page' => 1]
    )]
    public function listAction($page, EntityManagerInterface $entityManager): Response
    {
        /* if ($page <= 0) {
            throw $this->createNotFoundException('Erreur page introuvable , vérifiez votre ID ! Bonne journée !');

        }
        return $this->render('blog/list.html.twig', ['page' => $page]);*/
        if ($page <= 0) {
            throw $this->createNotFoundException('Erreur page introuvable , vérifiez votre ID ! Bonne journée!');
        }

        $articleRepository = $entityManager->getRepository(Article::class);
        $articlePerPage = $this->getParameter('articles_per_page');

        // Récupérez les articles publiés triés par date de création décroissante
        $articles = $articleRepository->findAllWithPage($page,$articlePerPage);

        $article = count($articles);
        $nbTotalPage = intval(ceil($article/$articlePerPage));

        return $this->render('blog/list.html.twig', ['articles' => $articles, 'page' => $page , 'totalpages'=>$nbTotalPage]);

    }

    #[Route('/article/{id}', name: 'viewAction',
        requirements: ['id' => '\d+'],
        defaults: ['id' => 1]
    )]
    public function viewAction($id, EntityManagerInterface $entityManager): Response
    {

        $articleRepository = $entityManager->getRepository(Article::class);


        $article = $articleRepository->find($id);

        // Vérifiez si l'article existe
        if (!$article) {
            throw  new NotFoundHttpException($message = "L'article n'a pas été trouvé.");
        }

        // Vérifiez si l'article est publié
        if (!$article->isPublished()) {
            throw  new NotFoundHttpException($message = "L'article n'est pas publié.");
        }

        return $this->render('blog/view.html.twig', ['article' => $article]);
        # return $this->render('blog/view.html.twig', ['id' => $id]);
    }

    #[Route('/article/add', name: 'addAction', methods: ['GET', 'POST'])]
    public function addAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        /*if (true) {
            #/ Traitement du formulaire...

            #/ Message de succès
            $this->addFlash('info', "L'article a été crée avec succés ! ");
            return $this->redirectToRoute('blog_listAction',);
        }
        return new Response("Formulaire d'ajout ! ");*/


        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('blog_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/add.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'editAction',
        requirements: ['id' => '\d+'],
        defaults: ['id => 1'])]
    public function editAction($id): Response
    {
        if (true) {
            #/ Traitement du formulaire...

            #/ Message de succès
            $this->addFlash('info', "L'article a été modifié avec succés ! ");
            return $this->redirectToRoute('blog_viewAction', ['id' => $id]);
        }
        return new Response("Formulaire d'ajout ! ");
    }

    #[Route('/article/delete/{id}', name: 'deleteAction',
        requirements: ['id' => '\d+'],
        defaults: ['id => 1'])]
    public function deleteAction($id, EntityManagerInterface $entityManager): Response
    {
        /*if (true) {
            #/ Traitement du formulaire...

            #/ Message de succès
            $this->addFlash('info', "L'article a été supprimé avec succés ! ");
            return $this->redirectToRoute('blog_listAction',['id' => $id]);
        }
        return new Response("Formulaire d'ajout ! ");*/

        $articleRepository = $entityManager->getRepository(Article::class);
        $article = $articleRepository->find($id);

        // Vérifiez si l'article existe
        if (!$article) {
            throw new NotFoundHttpException("L'article n'a pas été trouvé.");
        }

        // Utilisez remove() pour supprimer l'article
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('blog_listAction');

    }

    public function articleContenu($nbProducts): Response
    {
        $tab = [
            ['article' => "Article 1", 'id' => 1, 'content' => "test"],
            ['article' => "Article 2", 'id' => 2, 'content' => "test"],
            ['article' => "Article 3", 'id' => 3, 'content' => "test"],
        ];

        return $this->render('last_articles.html.twig', ['articles' => $tab]);
    }



}