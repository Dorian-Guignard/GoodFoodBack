<?php

namespace App\Controller\Api;

use App\Entity\Virtue;
use App\Repository\VirtueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
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
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class VirtueController extends AbstractController
{
    /**
     * @Route("/api/virtues", name="app_api_virtues",methods={"GET"})
     */
    public function index(VirtueRepository $virtueRepository): JsonResponse
    {
        $virtues = $virtueRepository->findAll();

        return $this->json([

            ['virtues' => $virtues],

            Response::HTTP_OK,

            [],

            ['groups' => "virtues_get_collection"]
        ]);
    }

    /**
     * Get virtues item
     * 
     * @Route("/api/virtues/{id<\d+>}", name="app_api_virtues_get_item", methods={"GET"})
     */
    public function getItem(Virtue $virtue = null): JsonResponse
    {

        if ($virtue === null) {

            return $this->json(['message' => 'Vertu non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(

            ['virtue' => $virtue],

            Response::HTTP_OK,

            [],

            ['groups' => 'virtues_get_item']
        );
    }


    /**
     * Update virtue
     * 
     * @Route("/api/virtues/{id<\d+>}", name="app_api_patch_virtues_item", methods={"PATCH"})
     */
    public function patch(Virtue $virtue = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        if ($virtue === null) {
            return $this->json(['message' => 'Vertu non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);


        $patchVirtue = $virtue
            ->setName($jsonContent['name'])
            ->setDescription($jsonContent['description'])
            ->setNameImage($jsonContent['nameImage']);


        $entityManager->persist($patchVirtue);

        $entityManager->flush();

        return $this->json(

            ['virtue' => $patchVirtue],

            Response::HTTP_OK,

            [],

            ['groups' => 'virtues_get_item']
        );
    }

    /**
     * Create virtue
     * 
     * @Route("/api/virtues", name="app_api_post_virtues_item", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {

        $jsonContent = $request->getContent();


        $virtue = $serializer->deserialize($jsonContent, Virtue::class, "json");
        
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
                    $this->getParameter('virtuePic_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                return $this->json(['error' => 'votre telechargement a échoué'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // updates the 'nameImage' property to store the image file name
            // instead of its contents
            $virtue->setNameImage(
                'images/virtuePic/' . $newFilename
            );
        }

        $errors = $validator->validate($virtue);

        $errorsList = [];
        if (count($errors) > 0) {

            foreach ($errors as $error) {

                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($virtue);

        $entityManager->flush();


        return $this->json(

            ['virtue' => $virtue],

            Response::HTTP_CREATED,

            [
                'Location' => $this->generateUrl(
                    'app_api_virtues_get_item',
                    ['id' => $virtue->getId()]
                )
            ],

            ['groups' => 'virtues_get_item']
        );
    }
    /**
     * Delete virtue
     * 
     * @Route("/api/virtues/{id<\d+>}", name="app_api_delete_virtues_item", methods={"DELETE"})
     */
    public function delete(Virtue $virtue = null, EntityManagerInterface $entityManager)
    {
        if ($virtue === null) {
            return $this->json(['message' => 'Vertu non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($virtue);
        $entityManager->flush();

        return $this->json(['message' => 'Vertu supprimée.'], Response::HTTP_OK);
    }
}
