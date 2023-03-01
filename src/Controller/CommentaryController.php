<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\CommentaryType;
use App\Repository\CommentaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentary')]
class CommentaryController extends AbstractController
{
    #[Route('/', name: 'app_commentary_index', methods: ['GET'])]
    public function index(CommentaryRepository $commentaryRepository): Response
    {
        return $this->render('commentary/index.html.twig', [
            'commentaries' => $commentaryRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_commentary_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Article $article, CommentaryRepository $commentaryRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->redirectToRoute('app_home');
        }
        $commentary = new Commentary();
        $commentary
            ->setArticle($article)
            ->setUser($this->getUser())
        ;
        $form = $this->createForm(CommentaryType::class, $commentary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaryRepository->save($commentary, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentary/new.html.twig', [
            'commentary' => $commentary,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commentary_show', methods: ['GET'])]
    public function show(Commentary $commentary): Response
    {
        return $this->render('commentary/show.html.twig', [
            'commentary' => $commentary,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_commentary_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentary $commentary, CommentaryRepository $commentaryRepository): Response
    {
        $form = $this->createForm(CommentaryType::class, $commentary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaryRepository->save($commentary, true);

            return $this->redirectToRoute('app_commentary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentary/edit.html.twig', [
            'commentary' => $commentary,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentary_delete', methods: ['POST'])]
    public function delete(Request $request, Commentary $commentary, CommentaryRepository $commentaryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentary->getId(), $request->request->get('_token'))) {
            $commentaryRepository->remove($commentary, true);
        }

        return $this->redirectToRoute('app_commentary_index', [], Response::HTTP_SEE_OTHER);
    }
}
