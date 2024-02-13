<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\Participants;
use App\Form\ParticipantsType;
use App\Repository\EventsRepository;
use App\Repository\ParticipantsRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/admin/participants', name:'admin_participants_')]
class ParticipantsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ParticipantsRepository $participantsRepository): Response
    {
        return $this->render('admin/participants/index.html.twig', [
            'participants' => $participantsRepository->findAll()
        ]);
    }

    /**
     * Add a user to be a participant of the selected event
     */
    #[Route('/participate/{id}', name: 'participate')]
    public function participate(Events $event, EventsRepository $eventsRepository, ParticipantsRepository $participantsRepository, Request $request, EntityManagerInterface $manager, UserInterface $userInterface, SendMailService $mail) : Response
    {
        // $event = $eventsRepository->findOneBy(['id' => $request->get('id')]);
        $participant = new Participants();

        $participant->setEvent($event);

        if($this->getUser()) {
            $participant->setUser($this->getUser());
            
            // Find if the current user is a participant of the event selected
            $verif = $participantsRepository->findOneBy(['user' => $this->getUser()]);
            if($verif != null && $verif->getEvent() == $event)
            {
                $this->addFlash('warning', 'Vous participez déjà à cet événement');
                return $this->redirectToRoute('acceuil_details', [
                    'id' => $event->getId(),
                ]);
            }
    
            else{
                $manager->persist($participant);
                $manager->flush();
                
                $mail->send(
                    'event@gmai.com',
                    $this->getUser()->getEMail(),
                    'Confirmation de votre participation pour l\'événement '.$event->getName(),
                    'participation',
                    [
                        'user' => $this->getUser(),
                        'event' => $event
                    ]
                    );
                $this->addFlash('success', 'Vous avez été ajouté avec succès comme participant');
                return $this->redirectToRoute('acceuil_details', [
                    'id' => $event->getId(),
                    'event' => $event
                ]);
            }
        }
        $this->addFlash('warning', 'Vous devez vous connecter avant de participer');
        return $this->redirectToRoute('acceuil_details', [
            'id' => $event->getId(),
            'event' => $event
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $participant = new Participants();
        $form = $this->createForm(ParticipantsType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setNote(0);
            $entityManager->persist($participant);
            $entityManager->flush();

            $mail->send(
                'gestion@gmail.com',
                $participant->getUser()->getEMail(),
                'Confirmation de votre participation pour l\'événement '.$participant->getUser()->getName(),
                'participation',
                [
                    'user' => $participant->getUser(),
                    'event' => $participant->getEvent()
                ]
                );

            $this->addFlash('success', 'Le participant a été ajouté avec succès');
            return $this->redirectToRoute('admin_participants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/participants/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Participants $participant): Response
    {
        return $this->render('admin/participants/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participants $participant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipantsType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_participants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/participants/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Participants $participant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_participants_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Give a note to an event
     */
    #[Route('/note/{id}/{note}', name: 'note')]
    public function note(Events $event, Request $request, EntityManagerInterface $entityManager, ParticipantsRepository $participantsRepository): Response
    {
        // We get the participant with the current user id and the current event as a parameter
        $participant = $participantsRepository->findOneBy([
            'user' => $this->getUser(),
            'event' => $event 
        ]);
        if($participant != null) {
            if($participant->getNote()){
                $participant->setNote($request->get('note'));
                $entityManager->flush();

                $this->addFlash('success', 'votre nouvelle note a été prise en compte');
                return $this->redirectToRoute('acceuil_details', [
                    'id' => $event->getId()
                ], Response::HTTP_SEE_OTHER);
            }
            $participant->setNote($request->get('note'));
            $entityManager->flush();

            $this->addFlash('success', 'vous venenez de noter cet événement');
            return $this->redirectToRoute('acceuil_details', [
                'id' => $event->getId()
            ], Response::HTTP_SEE_OTHER);
        }
        else {
            $this->addFlash('warnig', 'vous ne participez pas à cet événement, vous ne pouvez pas le noter');
            return $this->redirectToRoute('acceuil_details', [
                'id' => $event->getId()
            ], Response::HTTP_SEE_OTHER);
        }

    }
}
