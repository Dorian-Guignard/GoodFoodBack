<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    /**
     * Get recipes item
     * 
     * @Route("/api/recipes/{id<\d+>}", name="app_api_recipes_get_item", methods={"GET"})
     */
    public function getItem(Recipe $recipe = null): JsonResponse
    {

        if ($recipe === null) {

            return $this->json(['message' => 'recette non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(

            ['recipe' => $recipe],

            Response::HTTP_OK,

            [],

            ['groups' => 'recipes_get_item']
        );
    }

    /**
     * Update Recipe
     * 
     * @Route("/api/Recipes/{id<\d+>}", name="app_api_patch_Recipes_item", methods={"PATCH"})
     */
    public function patch(Recipe $Recipe = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        if ($Recipe === null) {
            return $this->json(['message' => 'Recette non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);

        $entityManager->persist($Recipe);

        $entityManager->flush();

        return $this->json(

            ['Recipe' => $Recipe],

            Response::HTTP_OK,

            [],

            ['groups' => 'Recipes_get_item']
        );
    }

    /**
     * Create recipe
     * 
     * @Route("/api/recipes", name="app_api_post_recipes_item", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        $jsonContent = $request->getContent();


        $recipe = $serializer->deserialize($jsonContent, Recipe::class, "json");
        var_dump($recipe);
        $errors = $validator->validate($recipe);

        $errorsList = [];
        if (count($errors) > 0) {

            foreach ($errors as $error) {

                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($recipe);

        $entityManager->flush();


        return $this->json(

            ['recipe' => $recipe],

            Response::HTTP_CREATED,

            [
                'Location' => $this->generateUrl(
                    'app_api_recipes_get_item',
                    ['id' => $recipe->getId()]
                )
            ],

            ['groups' => 'recipes_get_item']
        );
    }

}
