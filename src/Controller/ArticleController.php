<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\CommentaryType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, SluggerInterface $slugger, ParameterBagInterface $container): Response
    {

        if (!$this->isGranted('ROLE_AUTHOR')) {
            return $this->redirectToRoute('app_home');
        }

        $article = new Article();
        $article->setAuthor($this->getUser());

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug(strtolower($slugger->slug($form['title']->getData().uniqid())));
            $this->uploadImage($form['imageName'], $slugger, $article, $container, $articleRepository);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/{slug}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository,SluggerInterface $slugger, ParameterBagInterface $container): Response
    {
        if (!$this->isGranted('ROLE_AUTHOR') || $this->getUser() !== $article->getAuthor()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadImage($form['imageName'], $slugger, $article, $container, $articleRepository);

            return $this->redirectToRoute('app_article_show', ['slug'=> $article->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if (!$this->isGranted('ROLE_AUTHOR') || $this->getUser() !== $article->getAuthor()) {
            return $this->redirectToRoute('app_home');
        }
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param $imageName
     * @param SluggerInterface $slugger
     * @param Article $article
     * @param ParameterBagInterface $container
     * @param ArticleRepository $articleRepository
     * @return void
     */
    public function uploadImage($imageName, SluggerInterface $slugger, Article $article, ParameterBagInterface $container, ArticleRepository $articleRepository): void
    {
        $file = $imageName->getData();

        if ($file) {
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = $slugger->slug($originalFileName);
            $ext = $file->guessExtension();
            $newFileName = $safeFileName . '-' . uniqid() . $ext;
            $article->setImageName($newFileName);


            if (!$ext) {
                $ext = 'bin';
            }

            $file->move($container->get('upload.directory'), $newFileName . '.' . $ext);

        } else {
            $article->setImageName('defaultImage.png');
        }

        $articleRepository->save($article, true);
    }
}
