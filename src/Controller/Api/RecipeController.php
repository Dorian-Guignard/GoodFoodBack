<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\Virtue;
use App\Entity\Category;
use App\Entity\Composition;
use App\Repository\FoodRepository;
use App\Repository\RecipeRepository;
use App\Repository\VirtueRepository;
use App\Repository\CategoryRepository;
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
    public function index(Recipe $recipe = null, RecipeRepository $recipeRepository): JsonResponse
    {
        $recipes = $recipeRepository->findAll();


        if ($recipes === null) {
            return $this->json(['message' => 'recettes non trouvées.'], Response::HTTP_NOT_FOUND);
        }
        

        return $this->json(
            ['recipe' => $recipes],
            Response::HTTP_OK,
            [],
            ['groups' => "recipes_get_collection"]
        );
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

        $recipeData = [

            [
                'recipe' => $recipe,

                'category' => [
                    'id' => $recipe->getCategory()->getId(),
                    'name' => $recipe->getCategory()->getName()
                ],

                'virtue' => [
                    'id' => $recipe->getVirtue()->getId(),
                    'name' => $recipe->getVirtue()->getName(),
                    'description' => $recipe->getVirtue()
                ],
            ]
        ];

        return $this->json(

            ['recipe' => $recipeData],

            Response::HTTP_OK,

            [],

            ['groups' => 'recipes_get_item']
        );
    }

    /**
     * Update Recipe
     * 
     * @Route("/api/recipes/{id<\d+>}", name="app_api_patch_Recipes_item", methods={"PATCH"})
     */
    public function patch(Recipe $Recipe = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        if ($Recipe === null) {
            return $this->json(['message' => 'Recette non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);

        $patchRecipe = $Recipe
            ->setName($jsonContent['name'])
            ->setDescription($jsonContent['description'])
            ->setDuration($jsonContent['duration'])
            ->setHeatTime($jsonContent['heatTime'])
            ->setPrepTime($jsonContent['prepTime'])
            ->setPortion($jsonContent['portion'])
            ->setSteps($jsonContent['steps'])
            ->setPicture($jsonContent['picture']);

        $entityManager->persist($patchRecipe);

        $entityManager->flush();

        return $this->json(

            ['Recipe' => $patchRecipe],

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
    public function create(FoodRepository $foodRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, CategoryRepository $categoryRepo, VirtueRepository $virtueRepo)
    {

        $jsonContent = $request->getContent();
        $recipe = $serializer->deserialize($jsonContent, Recipe::class, "json", [
            'attributes_to_skip' => ['compositions', 'virtue', 'category']
        ]);
        // 1. Décoder les données de la requete -> mises dans un tableau associatif
        $decodedContent = json_decode($jsonContent, true);

        // On récupère les relations virtue et category par leurs id, puis on les set dans recipe.
        $virtueId = $decodedContent['virtue'];
        $categoryId = $decodedContent['category'];

        $category = $categoryRepo->find($categoryId);
        $virtue = $virtueRepo->find($virtueId);

        $recipe->setVirtue($virtue)->setCategory($category);

        $compositions = $decodedContent['compositions'];

        foreach ($compositions as $composition) {
            // 2. Recuperer food pour chaque composition à partir de son id
            $foodId = $composition['food'];
            $food = $foodRepository->find($foodId);

            // 3. Creer des instances de compositions pour chaques entrées du tableau de composition (et ajouter food recuper avec setFood())
            $newComposition = new Composition();
            $newComposition->setFood($food)->setQuantity($composition['quantity'])->setUnity($composition['unity']);

            // 4. Ajouter chaque composition à partir de recipe avec addComposition()
            $recipe->addComposition($newComposition);
        }



        $errors = $validator->validate($recipe);

        $errorsList = [];
        if (count($errors) > 0) {

            foreach ($errors as $error) {

                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        /* var_dump($recipe); */

        // 5. persist et flush
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
    /**
     * Delete recipe
     * 
     * @Route("/api/recipes/{id<\d+>}", name="app_api_delete_recipes_item", methods={"DELETE"})
     */
    public function delete(Recipe $recipe = null, EntityManagerInterface $entityManager)
    {
        if ($recipe === null) {
            return $this->json(['message' => 'Recette non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->json(['message' => 'Recette supprimée.'], Response::HTTP_OK);
    }
}
