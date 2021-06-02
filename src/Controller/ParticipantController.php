<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="participant")
     */
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    /**
     * @Route("/participant/detail/{id}", name="participant_detail", requirements={"id"="\d+"})
     */
    public function detail($id, ParticipantRepository $participantRepository, CampusRepository $campusRepository): Response
    {
        // TODO recupérer la serie en fonction de son id

        $participant = $participantRepository->find($id);
        if (!$participant) {
            // throw $this -> createNotFoundException("Oops ! This serie doesn't exist !");
            return $this->redirectToRoute('sortie_list');
        }

        $campus = $campusRepository->find($id);

        return $this->render('participant/detail.html.twig', [
            "campus" => $campus,
            "participant" => $participant
        ]);
    }

    /**
     * @Route("/participant/edit/{id}", name="participant_edit")
     */
    public function edit($id,
                         EntityManagerInterface $entityManager,
                         Request $request,
                         ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);
        if (!$participant) {
            throw $this->createNotFoundException("Oups ce participant n'éxiste pas");
        }

        $participantForm = $this->createForm(ParticipantType::class,$participant);
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()) { // dans cet ordre là

            // $this->getDoctrine()->getManager(); Deuxieme façon sans le metre en paramètre
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Participant modifié !!');
            return $this->redirectToRoute('participant_detail', ['id' => $participant->getId()]);
        }

        return $this->render('participant/edit.html.twig', [
            "participant"=> $participant,
            'participantForm' => $participantForm->createView()
        ]);

    }



}
