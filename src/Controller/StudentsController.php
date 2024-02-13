<?php

namespace App\Controller;

use App\Entity\Students;
use App\Form\StudentsType;
use App\Repository\StudentsRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/students', name: 'admin_students_')]
class StudentsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(StudentsRepository $studentsRepository): Response
    {
        return $this->render('admin/students/index.html.twig', [
            'students' => $studentsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $student = new Students();
        $form = $this->createForm(StudentsType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($student);
            $entityManager->flush();
            
            $mail->send(
                'gasagestion@gmail.com',
                $student->getEmail(),
                'retours sur inscription',
                'inscription',
                [
                    'student' => $student
                ]
            );

            return $this->redirectToRoute('admin_students_show', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/students/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Students $student): Response
    {
        return $this->render('admin/students/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Students $student, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StudentsType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_students_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/students/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Students $student, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_students_index', [], Response::HTTP_SEE_OTHER);
    }
}
