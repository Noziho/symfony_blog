<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
                '/defaultAvatar1',
                '/defaultAvatar2',
                '/defaultAvatar3',
                '/defaultAvatar4',
                '/defaultAvatar5',
                '/defaultAvatar6',
                '/defaultAvatar7',
                '/defaultAvatar8',
            ];

        $random = array_rand($avatars);

        return $avatars[$random];
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{email}', name: "app_user_delete")]
    public function delete(User $user, Request $request, UserRepository $userRepository): RedirectResponse
    {
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home');
        }

        $userRepository->remove($user, true);

        $request->getSession()->invalidate();
        $this->container->get('security.token_storage')->setToken();
        return $this->redirectToRoute('app_home');

    }


}
