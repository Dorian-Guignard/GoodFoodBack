<?php

namespace App\Controller\Api;

use App\Repository\FoodRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FoodController extends AbstractController
{
    /**
     * @Route("/api/foods", name="app_api_foods", methods={"GET"})
     */
    public function index(FoodRepository $foodRepository): JsonResponse
    {
       $foods = $foodRepository->findAll();

        return $this->json([

            ['foods' => $foods],

            Response::HTTP_OK,

            [],

            ['groups' => "foods_get_collection"  ]
        ]);
    }
}
