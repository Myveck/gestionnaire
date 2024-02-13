<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Events;
use App\Form\CommentFormType;
use App\Repository\CategoriesRepository;
use App\Repository\CommentsRepository;
use App\Repository\EventsRepository;
use App\Repository\ParticipantsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'acceuil_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        CategoriesRepository $categoriesRepository, 
        EventsRepository $eventsRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], [
                'id' => 'asc'
            ]),
            'events' => $eventsRepository->findBy([], [
                'categorie' => 'asc'
            ])
        ]);
    }

    #[Route('/gerer', name:'manage')]
    public function manager() : Response
    {
        return $this->render('main/manage.html.twig');
    }

    #[Route('/tableaudebord', name: 'tableau')]
    public function tableau(EventsRepository $events, ParticipantsRepository $participants, CommentsRepository $commentsRepository)
    {
        $les = $events->findAll();
        $note = array();
        $a = array();

        // On initialise toutes les notes à 0
        foreach ($les as $key1 => $value1) {
            $note[$value1->getName()] = 0;
            $a[$value1->getName()] = 0;
        };

        // Remplir les notes de chaque event
        foreach ($les as $key => $val) {
            foreach ($val->getParticipants() as $key2 => $value) {

                if(isset($note[$val->getName()])) {
                    $note[$val->getName()] = $note[$val->getName()] + (($value->getNote() == null ) ? 0 : $value->getNote());
                    $a[$val->getName()] = ($value->getNote() != null ) ? $a[$val->getName()] + 1 : $a[$val->getName()] + 0; 

                } else {
                    $note[$val->getName()] = (($value->getNote() == null ) ? 0 : $value->getNote());
                    $a[$val->getName()] = ($value->getNote() != null ) ? $a[$val->getName()] + 1 : $a[$val->getName()] + 0; 
                }
            }
        }

        // Calcul de la moyenne des notes d'un event en %
        foreach ($les as $cle => $valeur) {
            if($note[$valeur->getName()] > 0){
                $note[$valeur->getName()] = (($note[$valeur->getName()] / $a[$valeur->getName()]) / 5) * 100;
            }
        }

        return $this->render('main/tableau.html.twig', [
            'events' => $events->findAll(),
            'participants' => $participants->findBy([], [], 3),
            'comments' => $commentsRepository->findBy([], [], 5),
            'notes' => $note
        ]);
    }
    
    /**
     * Renvoie les détails de l'événement
     */
    #[Route('/details/{id}', name: 'details')]
    public function details(Events $event, Request $request, EntityManagerInterface $em, ParticipantsRepository $participantsRepository, CommentsRepository $commentsRepository): Response
    {
        $comment = new Comments();
        // $comments = $event->getComments();
        $commentForm = $this->createForm(CommentFormType::class, $comment);

        $participants = $event->getParticipants();

        $a = 0;
        $note = 0;

        if($participants){

            for ($i=0; $i < count($participants); $i++) { 
                $note+= $participants[$i]->getNote();
                ($participants[$i]->getNote() > 0 ) ? $a++ : $a+=0;
            }
            if($note > 0){
                $note = ($note / $a) / 5 * 100;
            }
        }


        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid())
        {
            $participant = $participantsRepository->findOneBy(['user' => $this->getUser()]);
            if($participant){
                if($participant->getEvent() === $event) {

                    $comment->setEvent($event);
                    $comment->setParticipant($participantsRepository->findOneBy(['user' => $this->getUser()]));
                    $em->persist($comment);
                    $em->flush();
                    return $this->redirectToRoute('acceuil_details', ['id' => $event->getId()]);
                }

            }

            $this->addFlash('warning', 'Seuls les participants sont autorisés à commenter');
            return $this->redirectToRoute('acceuil_details', ['id' => $event->getId()]);
        }

        return $this->render('admin/events/details.html.twig', [
            'event' => $event,
            'comments' => $commentsRepository->findBy(['event' => $event], ['comment_date' => 'desc']),
            'note' => $note,
            'commentForm' => $commentForm->createView()
        ]);
    }

    #[Route('/filtre/{id}', name: 'filtre')]
    public function filtre(Request $request, EventsRepository $eventsRepository, CategoriesRepository $categoriesRepository): Response
    {
        // Verification if the param received is date or not
        if($request->get('id') == 'date'){
            $events = $eventsRepository->findBy([], ['event_date' => 'desc']);
            $filtre = 'Date';
        } else {
            $categorie = $categoriesRepository->findOneBy(['id' => $request->get('id')]);
            $events = $eventsRepository->findBy(['categorie' => $categorie]);
            $filtre = $categorie->getName();
        }
        return $this->render('main/filtre.html.twig',[
            'events' => $events,
            'filtre' => $filtre,
            'categories' => $categoriesRepository->findAll()
        ]);
    }
}
