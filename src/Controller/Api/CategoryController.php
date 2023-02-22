<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    /**
     * @Route("/api/categories", name="app_api_categories", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        return $this->json([

            ['categories' => $categories],

            Response::HTTP_OK,

            [],

            ['groups' =>"categories_get_collection"  ]
        ]);
    }

    
}
