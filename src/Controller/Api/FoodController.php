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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
            ->setNameImage($jsonContent['nameImage']);


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
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $jsonContent = $request->getContent();


        $food = $serializer->deserialize($jsonContent, Food::class, "json");
         
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
                            $this->getParameter('foodPic_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        return $this->json(['error' => 'votre telechargement a échoué'], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
        
                    // updates the 'nameImage' property to store the image file name
                    // instead of its contents
                    $food->setNameImage(
                        'images/foodPic/' . $newFilename
                    );
                }
        
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
    /**
     * Delete food
     * 
     * @Route("/api/foods/{id<\d+>}", name="app_api_delete_foods_item", methods={"DELETE"})
     */
    public function delete(Food $food = null, EntityManagerInterface $entityManager)
    {
        if ($food === null) {
            return $this->json(['message' => 'ingrédient non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($food);

        $entityManager->flush();

        return $this->json(['message' => 'ingrédient supprimé.'], Response::HTTP_OK);
    }
}
