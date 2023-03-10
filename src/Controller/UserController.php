<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                       /**  @var UploadedFile $imageFile */
                       $imageFile = $form->get('nameImage')->getData();

                       // this condition is needed because the 'image' field is not required
                       // so the image file must be processed only when a file is uploaded
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
                               $this->addFlash('error', 'votre telechargement a échoué');
                               return $this->redirectToRoute('app_user_new');
                           }
           
                           // updates the 'nameImage' property to store the image file name
                           // instead of its contents
                           $user->setNameImage(
                               'images/userPic/' . $newFilename
                           );
           
                           //var_dump($newFilename);
                       }
                       $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {

        if ($user === null) {
            throw $this->createNotFoundException("Ce user n'existe pas");
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                                   /**  @var UploadedFile $imageFile */
                                   $imageFile = $form->get('nameImage')->getData();

                                   // this condition is needed because the 'image' field is not required
                                   // so the image file must be processed only when a file is uploaded
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
                                           $this->addFlash('error', 'votre telechargement a échoué');
                                           return $this->redirectToRoute('app_user_new');
                                       }
                       
                                       // updates the 'nameImage' property to store the image file name
                                       // instead of its contents
                                       $user->setNameImage(
                                           'images/userPic/' . $newFilename
                                       );
                       
                                       //var_dump($newFilename);
                                   }
                                   $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

}
