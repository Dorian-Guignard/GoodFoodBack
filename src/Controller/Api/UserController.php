<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/api/users", name="app_api_users",methods={"GET"})
     */
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json([

            ['users' => $users],

            Response::HTTP_OK,

            [],

            ['groups' => "users_get_collection"]
        ]);
    }

    /**
     * Get users item
     * 
     * @Route("/api/users/{id<\d+>}", name="app_api_users_get_item", methods={"GET"})
     */
    public function getItem(User $user = null): JsonResponse
    {

        if ($user === null) {

            return $this->json(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(

            ['user' => $user],

            Response::HTTP_OK,

            [],

            ['groups' => 'users_get_item']
        );
    }


    /**
     * Updatuser
     * 
     * @Route("/api/users/{id<\d+>}", name="app_api_patch_users_item", methods={"PATCH"})
     */
    public function patch(User $user = null, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {

        if ($user === null) {
            return $this->json(['message' => 'user non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = json_decode($request->getContent(), true);


        $patchUser = $user

            ->setPassword($jsonContent['password'])
            ->setEmail($jsonContent['email'])
            ->setRoles($jsonContent['roles'])
            ->setNameImage($jsonContent['nameImage'])
            ->setNameUser($jsonContent['nameUser']);


        $entityManager->persist($patchUser);

        $entityManager->flush();

        return $this->json(

            ['user' => $patchUser],

            Response::HTTP_OK,

            [],

            ['groups' => 'users_get_item']
        );
    }

    /**
     * Create user
     * 
     * @Route("/api/users", name="app_api_post_users_item", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger)
    {
        
        $data = $request->request->all();
        $imageFile = $request->files->get('imageFile');
        $user = $serializer->deserialize(json_encode($data), User::class, "json");

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            // Move the file to the directory where images are stored
            try {
                $imageFile->move(
                    $this->getParameter('userPic_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                return $this->json(['error' => 'votre telechargement a échoué'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // updates the 'nameImage' property to store the image file name
            // instead of its contents
            $user->setNameImage(
                'images/userPic/' . $newFilename
            );
        }
        // Hash the password
        $plainPassword = $user->getPassword();

        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);

        $errorsList = [];
        if (count($errors) > 0) {

            foreach ($errors as $error) {

                $errorsList[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($errorsList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($user);

        $entityManager->flush();


        return $this->json(

            ['user' => $user],

            Response::HTTP_CREATED,

            [
                'Location' => $this->generateUrl(
                    'app_api_users_get_item',
                    ['id' => $user->getId()]
                )
            ],

            ['groups' => 'users_get_item']
        );
    }
    /**
     * Delete user
     * 
     * @Route("/api/users/{id<\d+>}", name="app_api_delete_users_item", methods={"DELETE"})
     */
    public function delete(User $user = null, EntityManagerInterface $entityManager)
    {
        if ($user === null) {
            return $this->json(['message' => 'usernon trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'user supprimée.'], Response::HTTP_OK);
    }

    

    /**
     *@Route("/api/usersconnect", name="app_api__usersconnect_item", methods={"GET"}) 
     */
    public function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return $this->json(['message' => 'token non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => 'user non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(
            ['user' => $user],

            Response::HTTP_OK,

            [],

            ['groups' => 'users_get_item']
    );
    }
}