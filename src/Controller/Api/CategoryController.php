<?php

namespace App\Controller\Api;

use Symfony\Component\String\Slugger\SluggerInterface;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
            ->setNameImage($jsonContent['nameImage']);


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
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $jsonContent = $request->getContent();


        $category = $serializer->deserialize($jsonContent, Category::class, "json");
         
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
                            $this->getParameter('categoryPic_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        return $this->json(['error' => 'votre telechargement a échoué'], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
        
                    // updates the 'nameImage' property to store the image file name
                    // instead of its contents
                    $category->setNameImage(
                        'images/categoryPic/' . $newFilename
                    );
                }

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

    /**
     * Delete category
     * 
     * @Route("/api/categories/{id<\d+>}", name="app_api_delete_categories_item", methods={"DELETE"})
     */
    public function delete(Category $category = null, EntityManagerInterface $entityManager)
    {
        if ($category === null) {
            return $this->json(['message' => 'Categorie non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->json(['message' => 'La catégorie a été supprimée.'], Response::HTTP_OK);
    }
}
