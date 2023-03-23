<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleSwitcherController extends AbstractController
{
    #[Route(path: '/switch-locale/{locale}', name: 'app_switch_locale', methods: 'GET')]
    public function SwitchLocale($locale, Request $request): Response
    {
        $request->getSession()->set('_locale', $locale);
        return $this->redirect($request->headers->get('referer'));
    }

}