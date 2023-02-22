<?php

namespace App\Controller\Api;

use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * @Route("/api/recipes", name="app_api_recipes", methods={"GET"})
     */
    public function index(RecipeRepository $recipeRepository): JsonResponse
    {
        $recipes = $recipeRepository->findAll();

        return $this->json([

            ['recipes' => $recipes],

            Response::HTTP_OK,

            [],

            ['groups' =>"recipes_get_collection"]
        ]);
    }
}
