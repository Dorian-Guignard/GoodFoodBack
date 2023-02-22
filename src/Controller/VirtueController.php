<?php

namespace App\Controller;

use App\Entity\Virtue;
use App\Form\VirtueType;
use App\Repository\VirtueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function new(Request $request, VirtueRepository $virtueRepository): Response
    {
        $virtue = new Virtue();
        $form = $this->createForm(VirtueType::class, $virtue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        return $this->render('virtue/show.html.twig', [
            'virtue' => $virtue,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_virtue_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Virtue $virtue, VirtueRepository $virtueRepository): Response
    {
        $form = $this->createForm(VirtueType::class, $virtue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
