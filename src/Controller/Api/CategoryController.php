<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

            ['groups' => "categories_get_collection"]
        ]);
    }
    /**
     * Get categories item
     * 
     * @Route("/api/categories/{id<\d+>}", name="app_api_categories_get_item", methods={"GET"})
     */
    public function getItem(Category $category = null): JsonResponse
    {

        if ($category === null) {

            return $this->json(['message' => 'categorie non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(

            ['category' => $category],

            Response::HTTP_OK,

            [],

            ['groups' => 'categories_get_item']
        );
    }

    /**
     * Update categorie
     * 
     * @Route("/api/categories/{id<\d+>}", name="app_api_patch_categories_item", methods={"PATCH"})
     */
    public function patch(Category $category = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        if ($category === null) {
            return $this->json(['message' => 'Categorie non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);

        $patchCategory = $category
            ->setName($jsonContent['name'])
            ->setPicture($jsonContent['picture']);


        $entityManager->persist($patchCategory);

        $entityManager->flush();

        return $this->json(

            ['category' => $patchCategory],

            Response::HTTP_OK,

            [],

            ['groups' => 'categories_get_item']
        );
    }
    /**
     * Create category
     * 
     * @Route("/api/categories", name="app_api_post_categories_item", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $jsonContent = $request->getContent();


        $category = $serializer->deserialize($jsonContent, Category::class, "json");
        var_dump($category);
        $errors = $validator->validate($category);

        $errorsList = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($category);

        $entityManager->flush();


        return $this->json(
            ['category' => $category],
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'app_api_categories_get_item',
                    ['id' => $category->getId()]
                )
            ],
            ['groups' =>
            'categories_get_item']
        );
    }
}
