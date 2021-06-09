<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $isAdmin = $this->isGranted("ROLE_ADMIN");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux administrateurs de ce site!");
        }

        $participant = $this->getUser();
        $newParticipant = new Participant();
        $newParticipant->setRoles(["ROLE_PARTICIPANT"]);
        $form = $this->createForm(RegistrationFormType::class, $newParticipant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $newParticipant->setPassword(
                $passwordEncoder->encodePassword(
                    $newParticipant,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newParticipant);
            $entityManager->flush();

            $this->addFlash('success', 'Participant créé avec succès!!');
            // do anything else you need here, like send an email

            // return $guardHandler->authenticateUserAndHandleSuccess(
            //     $participant,
            //     $request,
            //     $authenticator,
            //     'main' // firewall name in security.yaml
            // );
        }
        return $this->render('participant/register.html.twig', [
            'registrationForm' => $form->createView(),
            'participant' => $participant
        ]);
    }
}
