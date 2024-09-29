<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Form\PostType;
use App\Repository\SectionRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'title' => 'Homepage',
            'homepage_text'=> "Nous somme le ".date('d/m/Y \à H:i'),
            'posts' => $postRepository->findAll(),
        ]);
    }


    //Création de l'url pour le détail d'une section
    #[Route(
        path: '/section/{id}',
        name: 'section',
        requirements: ['id' => '\d+'],
        defaults: ['id'=>1])]

    public function section(SectionRepository $sections, int $id): Response
    {
        $section = $sections->find($id);
        return $this->render('main/section.html.twig', [
            'title' => 'Section => '.$section->getSectionTitle(),
            'homepage_text' => $section->getSectionDescription(),
            'section' => $section,
            'sections' => $sections->findAll()
        ]);
    }
}
