<?php

namespace App\Controller;

use App\Entity\Images;
use App\Form\ImagesType;
use App\Repository\ImagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/images', name: 'admin_images_')]
class ImagesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ImagesRepository $imagesRepository): Response
    {
        return $this->render('admin/images/index.html.twig', [
            'images' => $imagesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $image = new Images();
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('admin_images_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/images/new.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Images $image): Response
    {
        return $this->render('admin/images/show.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Images $image, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_images_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/images/edit.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Images $image, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_images_index', [], Response::HTTP_SEE_OTHER);
    }
}
