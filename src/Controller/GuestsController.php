<?php

namespace App\Controller;

use App\Entity\Guests;
use App\Form\GuestsType;
use App\Repository\GuestsRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/guests', name: 'admin_guests_')]
class GuestsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(GuestsRepository $guestsRepository): Response
    {
        return $this->render('admin/guests/index.html.twig', [
            'guests' => $guestsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $guest = new Guests();
        $form = $this->createForm(GuestsType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($guest);
            $entityManager->flush();

            $mail->send(
                $guest->getEvents()->getName().'@gmai.com',
                $guest->getEmail(),
                'Confirmation de votre participation pour l\'événement '.$guest->getEvents()->getName(),
                'participation',
                [
                    'user' => $guest,
                    'event' => $guest->getEvents()
                ]
                );

            return $this->redirectToRoute('admin_guests_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/guests/new.html.twig', [
            'guest' => $guest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Guests $guest): Response
    {
        return $this->render('admin/guests/show.html.twig', [
            'guest' => $guest,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Guests $guest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GuestsType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_guests_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/guests/edit.html.twig', [
            'guest' => $guest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Guests $guest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$guest->getId(), $request->request->get('_token'))) {
            $entityManager->remove($guest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_guests_index', [], Response::HTTP_SEE_OTHER);
    }
}
