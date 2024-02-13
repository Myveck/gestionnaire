<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/comments', name: 'admin_comments_')]
class CommentsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CommentsRepository $commentsRepository): Response
    {
        return $this->render('admin/comments/index.html.twig', [
            'comments' => $commentsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('admin_comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Comments $comment): Response
    {
        return $this->render('admin/comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('acceuil_details', ['id' => $comment->getEvent()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('acceuil_details', ['id' => $comment->getEvent()->getId()], Response::HTTP_SEE_OTHER);
    }
}
