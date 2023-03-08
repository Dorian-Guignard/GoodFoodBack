<?php

namespace App\Controller;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="app_category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CategoryRepository $categoryRepository, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category, true);

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
                                    $this->getParameter('categoryPic_directory'),
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                                $this->addFlash('error', 'votre telechargement a échoué');
                                return $this->redirectToRoute('app_category_new');
                            }
            
                            // updates the 'nameImage' property to store the image file name
                            // instead of its contents
                            $category->setNameImage(
                                'images/categoryPic/' . $newFilename
                            );
            
                            //var_dump($newFilename);
                        }
                        $categoryRepository->add($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {

        if ($category === null) {
            throw $this->createNotFoundException("Cette category n'existe pas");
        }
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository, SluggerInterface $slugger ): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category, true);

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
                                                $this->getParameter('categoryPic_directory'),
                                                $newFilename
                                            );
                                        } catch (FileException $e) {
                                            // ... handle exception if something happens during file upload
                                            $this->addFlash('error', 'votre telechargement a échoué');
                                            return $this->redirectToRoute('app_category_edit');
                                        }
                        
                                        // updates the 'nameImage' property to store the image file name
                                        // instead of its contents
                                        $category->setNameImage(
                                            'images/categoryPic/' . $newFilename
                                        );
                        
                                        //var_dump($newFilename);
                                    }
                                    $categoryRepository->add($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
