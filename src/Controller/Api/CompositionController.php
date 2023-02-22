<?php

namespace App\Controller\Api;

use App\Repository\CompositionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompositionController extends AbstractController
{
    /**
     * @Route("/api/compositions", name="app_api_compositions", methods={"GET"})
     */
    public function index(CompositionRepository $compositionRepository): JsonResponse
    {
       $compositions = $compositionRepository->findAll();

        return $this->json([

            ['compositions' => $compositions],

            Response::HTTP_OK,

            [],

            ['groups' =>"compositions_get_collection"]
        ]);
    }
}