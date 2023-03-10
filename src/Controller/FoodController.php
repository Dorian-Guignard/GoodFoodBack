<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/food")
 */
class FoodController extends AbstractController
{
    /**
     * @Route("/", name="app_food_index", methods={"GET"})
     */
    public function index(FoodRepository $foodRepository): Response
    {
        return $this->render('food/index.html.twig', [
            'food' => $foodRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_food_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FoodRepository $foodRepository, SluggerInterface $slugger): Response
    {
        $food = new Food();
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foodRepository->add($food, true);

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
                                    $this->getParameter('foodPic_directory'),
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                                $this->addFlash('error', 'votre telechargement a échoué');
                                return $this->redirectToRoute('app_food_new');
                            }
            
                            // updates the 'nameImage' property to store the image file name
                            // instead of its contents
                            $food->setNameImage(
                                'images/foodPic/' . $newFilename
                            );
            
                            //var_dump($newFilename);
                        }
                        $foodRepository->add($food, true);

            return $this->redirectToRoute('app_food_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('food/new.html.twig', [
            'food' => $food,
            'form' => $form,
        ]);
    }

    /**
     *
     * @Route("/{id}", name="app_food_show", methods={"GET"})
     */
    public function show(Food $food): Response
    {

        if ($food === null) {
            return $this->createNotFoundException();
            
        }
        return $this->render('food/show.html.twig', [
            'food' => $food,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_food_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Food $food, FoodRepository $foodRepository,SluggerInterface $slugger ): Response
    {
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foodRepository->add($food, true);

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
                                                $this->getParameter('foodPic_directory'),
                                                $newFilename
                                            );
                                        } catch (FileException $e) {
                                            // ... handle exception if something happens during file upload
                                            $this->addFlash('error', 'votre telechargement a échoué');
                                            return $this->redirectToRoute('app_food_edit');
                                        }
                        
                                        // updates the 'nameImage' property to store the image file name
                                        // instead of its contents
                                        $food->setNameImage(
                                            'images/foodPic/' . $newFilename
                                        );
                        
                                        //var_dump($newFilename);
                                    }
                                    $foodRepository->add($food, true);

            return $this->redirectToRoute('app_food_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('food/edit.html.twig', [
            'food' => $food,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_food_delete", methods={"POST"})
     */
    public function delete(Request $request, Food $food, FoodRepository $foodRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$food->getId(), $request->request->get('_token'))) {
            $foodRepository->remove($food, true);
        }

        return $this->redirectToRoute('app_food_index', [], Response::HTTP_SEE_OTHER);
    }
}
