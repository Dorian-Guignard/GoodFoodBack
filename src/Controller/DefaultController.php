<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/default")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    // src/Controller/DefaultController.php
    public function notFound(): Response
    {
        return $this->render('bundles/Exception/error404.html.twig', [
            'message' => 'Page non trouvée',
        ]);
    }


}
