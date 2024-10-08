<?php

namespace App\Controller;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Form\PostType;
use App\Repository\SectionRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(PostRepository $postRepository, SectionRepository $sectionRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'title' => 'Homepage',
            'homepage_text'=> "Nous somme le ".date('d/m/Y \à H:i'),
            'posts' => $postRepository->findAll(),
            'sections' => $sectionRepository->findAll(),
        ]);
    }


    //Création de l'url pour le détail d'une section
    #[Route(
        path: '/section/{id}',
        name: 'section',
        requirements: ['id' => '\d+'],
        defaults: ['id'=>1])]

    public function section(SectionRepository $sections, TagRepository $tags, int $id): Response
    {
        $tag = $tags->find($id);
        $section = $sections->find($id);
        return $this->render('main/section.html.twig', [
            'title' => 'Section => '.$section->getSectionTitle(),
            'homepage_text' => $section->getSectionDescription(),
            'section' => $section,
            'tag' => $tag,
            'sections' => $sections->findAll()
        ]);
    }


    #[Route(
        path: '/tag/{id}',
        name: 'tag',
        requirements: ['id' => '\d+'],
        defaults: ['id'=>1])]

    public function tag(SectionRepository $sections, TagRepository $tags, int $id): Response
    {
        $tag = $tags->find($id);
        return $this->render('main/tag.html.twig', [
            'title' => 'Section => '.$tag->getTagName(),
            'homepage_text' => $tag->getTagSlug(),
            'tag' => $tag,
            'tags' => $tags->findAll(),
            'sections' => $sections->findAll()
        ]);
    }

    #[Route(
        path: '/post/{id}',
        name: 'post',
        requirements: ['id' => '\d+'],
        defaults: ['id'=>1])]

    public function post(PostRepository $postRepository,SectionRepository $sections, TagRepository $tags, PostRepository $posts, int $id): Response
    {
        $post = $posts->find($id);
        $tag = $tags->find($id);
        $section = $sections->find($id);
        return $this->render('main/post.html.twig', [
            'title' => $post->getPostTitle(),
            'homepage_text' => $post->getPostText(),
            'posts' => $postRepository->findAll(),
            'tag' => $tag,
            'post' => $post,
            'section' => $section,
            'tags' => $tags->findAll(),
            'sections' => $sections->findAll()
        ]);
    }
}
