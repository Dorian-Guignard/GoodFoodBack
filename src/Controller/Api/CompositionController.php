<?php

namespace App\Controller\Api;

use App\Entity\Composition;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompositionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompositionController extends AbstractController
{
    /**
     * @Route("/api/compositions", name="app_api_compositions", methods={"GET"})
     */
    public function index(CompositionRepository $compositionRepository): JsonResponse
    {
        $compositions = $compositionRepository->findAll();

        return $this->json([

            ['compositions' => $compositions],

            Response::HTTP_OK,

            [],

            ['groups' => "compositions_get_collection"]
        ]);
    }
    /**
     * Get compositions item
     * 
     * @Route("/api/compositions/{id<\d+>}", name="app_api_compositions_get_item", methods={"GET"})
     */
    public function getItem(Composition $composition = null): JsonResponse
    {

        if ($composition === null) {

            return $this->json(['message' => 'composition non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(

            ['composition' => $composition],

            Response::HTTP_OK,

            [],

            ['groups' => 'compositions_get_item']
        );
    }
    /**
     * Update composition
     * 
     * @Route("/api/compositions/{id<\d+>}", name="app_api_patch_compositions_item", methods={"PATCH"})
     */
    public function patch(Composition $composition = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        if ($composition === null) {
            return $this->json(['message' => 'Composition non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);

        $entityManager->persist($composition);

        $entityManager->flush();

        return $this->json(

            ['composition' => $composition],

            Response::HTTP_OK,

            [],

            ['groups' => 'compositions_get_item']
        );
    }
    
    /**
     * Create composition
     * 
     * @Route("/api/compositions", name="app_api_post_compositions_item", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        $jsonContent = $request->getContent();


        $composition = $serializer->deserialize($jsonContent, Composition::class, "json");
        var_dump($composition);
        $errors = $validator->validate($composition);

        $errorsList = [];
        if (count($errors) > 0) {

            foreach ($errors as $error) {

                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($composition);

        $entityManager->flush();


        return $this->json(

            ['composition' => $composition],

            Response::HTTP_CREATED,

            [
                'Location' => $this->generateUrl(
                    'app_api_compositions_get_item',
                    ['id' => $composition->getId()]
                )
            ],

            ['groups' => 'compositions_get_item']
        );
    }
}
