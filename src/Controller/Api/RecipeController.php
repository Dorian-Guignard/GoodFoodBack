<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\Virtue;
use App\Entity\Category;
use App\Entity\Composition;
use App\Repository\FoodRepository;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use App\Repository\VirtueRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;


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

        if (isset($jsonContent['name'], $jsonContent['description'], $jsonContent['duration'], $jsonContent['heatTime'], $jsonContent['prepTime'], $jsonContent['portion'], $jsonContent['nameImage'])) {
            $patchRecipe = $Recipe
                ->setName($jsonContent['name'])
                ->setDescription($jsonContent['description'])
                ->setDuration($jsonContent['duration'])
                ->setHeatTime($jsonContent['heatTime'])
                ->setPrepTime($jsonContent['prepTime'])
                ->setPortion($jsonContent['portion'])
                ->setPicture($jsonContent['picture'])
                ->setNameImage($jsonContent['nameImage']);
        } else {
            throw new \Exception('Données de requête manquantes ou invalides');
        }

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
    public function create(
        FoodRepository $foodRepository,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        VirtueRepository $virtueRepository,
        UserRepository $userRepository,
        SluggerInterface $slugger
    ) {
        $user = $this->getUser();

        $jsonContent = $request->getContent();

        $recipe = $serializer->deserialize(
            $jsonContent,
            Recipe::class,
            "json",
            [AbstractNormalizer::IGNORED_ATTRIBUTES =>
            ['category', 'virtue', 'compositions'], AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,]
        );


        $user = $this->getUser();
        $jsonContent = $request->getContent();
        $recipe = $serializer->deserialize($jsonContent, Recipe::class, "json", [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category', 'virtue', 'compositions'], AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,]);
        $recipe->setUser($user);

        $decodedContent = json_decode($jsonContent, true);

        // On set manuellement category et virtue à partir de leurs id
        $recipe->setCategory($categoryRepository->find($decodedContent['category']))
            ->setVirtue($virtueRepository->find($decodedContent['virtue']));


        // On crée chaque composition et on l'ajoute à recipe
        foreach ($decodedContent['compositions'] as $compositionData) {
            $composition = new Composition();
            $composition->setFood($foodRepository->find($compositionData['food']))
                ->setUnity($compositionData['unity'])->setQuantity($compositionData['quantity']);
            $recipe->addComposition($composition);
        }
        $recipe->addComposition($composition);


        // Gestion de l'image
        $imageFile = $request->files->get('nameImage');
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            // Move the file to the directory where images are stored
            try {
                $imageFile->move(
                    $this->getParameter('new_item_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                return $this->json(['error' => 'votre telechargement a échoué'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // updates the 'nameImage' property to store the image file name
            // instead of its contents
            $recipe->setNameImage(
                'images/new_item/' . $newFilename
            );
        }

        $errors = $validator->validate($recipe);

        if (count($errors) > 0) {
            $errorsList = [];
            foreach ($errors as $error) {
                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Persist and flush the recipe
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
