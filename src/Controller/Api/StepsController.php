<?php

namespace App\Controller\Api;

use App\Entity\Steps;
use App\Repository\StepsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StepsController extends AbstractController
{
    /**
     * @Route("/api/steps", name="app_api_steps", methods={"GET"})
     */
    public function index(StepsRepository $stepsRepository): JsonResponse
    {
        $steps = $stepsRepository->findAll();

        return $this->json(

            ['stepss' => $steps],

            Response::HTTP_OK,

            [],

            ['groups' => "steps_get_collection"]
        );
    }

    /**
     * Get steps item
     * 
     * @Route("/api/steps/{id<\d+>}", name="app_api_steps_get_item", methods={"GET"})
     */
    public function getItem(Steps $steps = null): JsonResponse
    {

        if ($steps === null) {

            return $this->json(['message' => 'etape non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(

            ['steps' => $steps],

            Response::HTTP_OK,

            [],

            ['groups' => 'steps_get_item']
        );
    }

    /**
     * Update steps
     * 
     * @Route("/api/steps/{id<\d+>}", name="app_api_patch_steps_item", methods={"PATCH"})
     */
    public function patch(Steps $steps = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        if ($steps === null) {
            return $this->json(['message' => 'etape non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);

        $patchSteps = $steps
            ->setName($jsonContent['name'])
            ->setContent($jsonContent['content']);
            


        $entityManager->persist($patchSteps);

        $entityManager->flush();

        return $this->json(

            ['steps' => $patchSteps],

            Response::HTTP_OK,

            [],

            ['groups' => 'steps_get_item']
        );
    }

    /**
     * Create steps
     * 
     * @Route("/api/steps", name="app_api_post_steps_item", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        $jsonContent = $request->getContent();


        $steps = $serializer->deserialize($jsonContent, Steps::class, "json");

        $errors = $validator->validate($steps);

        $errorsList = [];
        if (count($errors) > 0) {

            foreach ($errors as $error) {

                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($steps);

        $entityManager->flush();


        return $this->json(

            ['steps' => $steps],

            Response::HTTP_CREATED,

            [
                'Location' => $this->generateUrl(
                    'app_api_steps_get_item',
                    ['id' => $steps->getId()]
                )
            ],

            ['groups' => 'steps_get_item']
        );
    }

    /**
     * Delete steps
     * 
     * @Route("/api/steps/{id<\d+>}", name="app_api_delete_steps_item", methods={"DELETE"})
     */
    public function delete(Steps $steps = null, EntityManagerInterface $entityManager)
    {
        if ($steps === null) {
            return $this->json(['message' => 'etape non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($steps);
        $entityManager->flush();

        return $this->json(['message' => 'etape supprimée.'], Response::HTTP_OK);
    }
}
