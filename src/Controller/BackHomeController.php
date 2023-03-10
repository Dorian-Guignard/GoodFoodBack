<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackHomeController extends AbstractController
{
    /**
     * @Route("/backhome", name="app_backhome")
     */
    public function index(): Response
    {
        return $this->render('backhome/backhome.html.twig', [
            'controller_name' => 'BackHomeController',
        ]);
    }
}
