<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/random')]
    public function getRandomAvatar(): string
    {
        $avatars =
            [
                '/build/images/avatar1',
                '/build/images/avatar2',
                '/build/images/avatar3',
                '/build/images/avatar4',
                '/build/images/avatar5',
                '/build/images/avatar6',
                '/build/images/avatar7',
                '/build/images/avatar8',
            ];

        $random = array_rand($avatars);

        return $avatars[$random];
    }
}
