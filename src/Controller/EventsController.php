<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\Images;
use App\Form\EventsType;
use App\Repository\EventsRepository;
use App\Repository\UsersRepository;
use App\Service\PictureService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/events', name: 'admin_events_')]
class EventsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EventsRepository $eventsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/events/index.html.twig', [
            'events' => $eventsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SendMailService $mail, UsersRepository $usersRepository, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $event = new Events();
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // take pictures
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                // define the destination directory
                $folder = 'products';

                // We call the service that will handle the picture
                $fichier = $pictureService->add($image, $folder, 300, 300);

                // Initialization of image
                $img = new Images();
                $img->setName($fichier);
                $event->addImage($img);
            }
            $entityManager->persist($event);
            $entityManager->flush(); 
            
            // Send a mail to alert all users of the new event
            $users = $usersRepository->findAll();
            foreach ($users as $key => $value) {
                $mail->send(
                    'event@gmail.com',
                    $value->getEmail(),
                    'Nouvel \'événement !',
                    'newevent',
                    [
                        'event' => $event,
                        'user' => $value
                    ]
                );
            }

            return $this->redirectToRoute('admin_events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/events/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Events $event): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/events/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Events $event, EntityManagerInterface $entityManager, SendMailService $mail, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // take pictures
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                // define the destination directory
                $folder = 'products';

                // We call the service 
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $event->addImage($img);
            }
            $entityManager->flush();

            // We take all the event's participant to alert them about the event change 
            $participants = $event->getParticipants();
            foreach ($participants as $key => $value) {
                $mail->send(
                    'event@gmail.com',
                    $value->getUser()->getEmail(),
                    'Changement sur l\'événement '.$event->getName(),
                    'eventchange',
                    [
                        'event' => $event,
                        'user' => $value->getUser()
                    ]
                );
            }

            $this->addFlash('success', 'l\'événement a été modifier avec succès');
            return $this->redirectToRoute('admin_events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/events/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Events $event, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_events_index', [], Response::HTTP_SEE_OTHER);
    }
}
