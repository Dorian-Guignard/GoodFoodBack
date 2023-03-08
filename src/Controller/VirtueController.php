<?php

namespace App\Controller;

use App\Entity\Virtue;
use App\Form\VirtueType;
use App\Repository\VirtueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/virtue")
 */
class VirtueController extends AbstractController
{
    /**
     * @Route("/", name="app_virtue_index", methods={"GET"})
     */
    public function index(VirtueRepository $virtueRepository): Response
    {
        return $this->render('virtue/index.html.twig', [
            'virtues' => $virtueRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_virtue_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VirtueRepository $virtueRepository, SluggerInterface $slugger): Response
    {
        $virtue = new Virtue();
        $form = $this->createForm(VirtueType::class, $virtue);
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
                                    $this->getParameter('virtuePic_directory'),
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                                $this->addFlash('error', 'votre telechargement a échoué');
                                return $this->redirectToRoute('app_virtue_new');
                            }
            
                            // updates the 'nameImage' property to store the image file name
                            // instead of its contents
                            $virtue->setNameImage(
                                'images/virtuePic/' . $newFilename
                            );
            
                            //var_dump($newFilename);
                        }
                        $virtueRepository->add($virtue, true);

            return $this->redirectToRoute('app_virtue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('virtue/new.html.twig', [
            'virtue' => $virtue,
            'form' => $form,
        ]);
    }




    /**
     * @Route("/{id}", name="app_virtue_show", methods={"GET"})
     */
    public function show(Virtue $virtue): Response
    {

        if ($virtue === null) {
            throw $this->createNotFoundException("Cette vertue n'existe pas");
        }
        return $this->render('virtue/show.html.twig', [
            'virtue' => $virtue,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_virtue_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Virtue $virtue, VirtueRepository $virtueRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(VirtueType::class, $virtue);
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
                                               $this->getParameter('virtuePic_directory'),
                                               $newFilename
                                           );
                                       } catch (FileException $e) {
                                           // ... handle exception if something happens during file upload
                                           $this->addFlash('error', 'votre telechargement a échoué');
                                           return $this->redirectToRoute('app_virtue_new');
                                       }
                       
                                       // updates the 'nameImage' property to store the image file name
                                       // instead of its contents
                                       $virtue->setNameImage(
                                           'images/virtuePic/' . $newFilename
                                       );
                       
                                       //var_dump($newFilename);
                                   }
                                   $virtueRepository->add($virtue, true);

            return $this->redirectToRoute('app_virtue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('virtue/edit.html.twig', [
            'virtue' => $virtue,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_virtue_delete", methods={"POST"})
     */
    public function delete(Request $request, Virtue $virtue, VirtueRepository $virtueRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$virtue->getId(), $request->request->get('_token'))) {
            $virtueRepository->remove($virtue, true);
        }

        return $this->redirectToRoute('app_virtue_index', [], Response::HTTP_SEE_OTHER);
    }
}
