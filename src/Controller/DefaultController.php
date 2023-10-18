<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/', name: 'app_default_')]
class DefaultController extends AbstractController
{
#[Route('', name: 'home')]
    public function homeAction(): Response
    {
        return  $this->render('blog/blog.html.twig');
    }
    #[Route('/a-propos', name: 'about')]
    public function aboutAction(): Response
    {
        return new Response('ABOUT ! ');
    }

}