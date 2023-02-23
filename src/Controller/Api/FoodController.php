<?php

namespace App\Controller\Api;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

            ['groups' => "foods_get_collection"]
        ]);
    }
    /**
     * Get foods item
     * 
     * @Route("/api/foods/{id<\d+>}", name="app_api_foods_get_item", methods={"GET"})
     */
    public function getItem(Food $food = null): JsonResponse
    {

        if ($food === null) {

            return $this->json(['message' => 'ingredient non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(

            ['food' => $food],

            Response::HTTP_OK,

            [],

            ['groups' => 'foods_get_item']
        );
    }
    /**
     * Update food
     * 
     * @Route("/api/foods/{id<\d+>}", name="app_api_patch_foods_item", methods={"PATCH"})
     */
    public function patch(Food $food = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        if ($food === null) {
            return $this->json(['message' => 'ingrédient non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);

        $patchFood = $food
            ->setName($jsonContent['name'])
            ->setPicture($jsonContent['picture']);


        $entityManager->persist($patchFood);

        $entityManager->flush();

        return $this->json(

            ['food' => $patchFood],

            Response::HTTP_OK,

            [],

            ['groups' => 'foods_get_item']
        );
    }

    /**
     * Create food
     * 
     * @Route("/api/foods", name="app_api_post_foods_item", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $jsonContent = $request->getContent();


        $food = $serializer->deserialize($jsonContent, Food::class, "json");

        $errors = $validator->validate($food);

        $errorsList = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($food);

        $entityManager->flush();


        return $this->json(
            ['food' => $food],
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'app_api_foods_get_item',
                    ['id' => $food->getId()]
                )
            ],
            ['groups' => 'foods_get_item']
        );
    }
}
