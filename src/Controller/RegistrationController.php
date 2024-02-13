<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            
            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'groupeMyveck@gmail.com',
                $user->getEmail(),
                'Activation de votre compte sur le site de gestionnaire d\'événement',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Verif the user by sending a mail
     */

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $manager): Response
    {
        if($jwt->isValid($token) && !$jwt->isExperied($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $jwt->getPayload($token);
            $user = $usersRepository->find($payload['user_id']);

            if($user && !$user->isIsVerified()) {
                $user->setIsVerified(true);
                $manager->flush($user);

                $this->addFlash('success', 'Votre compte a été activé');
                return $this->redirectToRoute('acceuil_index');
            }
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/resendverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository) : Response
    {
        $user = $this->getUser();

        if(!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login'); 
        }
        if($user->isIsVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('acceuil_index');
        }
        
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        
        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $mail->send(
            'groupeMyveck@gmail.com',
            $user->getEmail(),
            'Activation de votre compte sur le site de gestionnaire d\'événement',
            'register',
            compact('user', 'token')
        );
        $this->addFlash('success', 'Le lien d\'activation a été renvoyé avec succès');
        return $this->redirectToRoute('acceuil_index');
    }
}
