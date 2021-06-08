<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\ManageEntity\UpdateEntity;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        //$participant= $this->getUser();
        if (!$participant) {
            // throw $this -> createNotFoundException("Oops ! Ce participant n'éxiste pas !");
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
                         UserPasswordEncoderInterface $passwordEncoder,
                         ParticipantRepository $participantRepository,
                         CampusRepository $campusRepository): Response
    {


       // $participant = $participantRepository->find($id);
        $participant= $this->getUser();

        $campus = $campusRepository->find($id);
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);
        $entityManager->refresh($participant);

        if ($participantForm->isSubmitted() && $participantForm->isValid()) { // dans cet ordre là

            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $participantForm->get('password')->getData()));

            //IMAGE
            $file = $participantForm->get('urlPhoto')->getData();
            /**
             * @var UploadedFile $file
             */
            if($file) {
                $newFileName = $participant->getNom().'-'.uniqid().'.'.$file->guessExtension();
                $file->move($this->getParameter('upload_image_participant'), $newFileName);
                $participant->setUrlPhoto($newFileName);
            }

            // $this->getDoctrine()->getManager(); Deuxieme façon sans le metre en paramètre
            $entityManager->persist($participant);
            $entityManager->flush();


            $this->addFlash('success', 'Profil correctement modifié !!');
            return $this->redirectToRoute('sortie_list', ['id' => $participant->getId()]);
        }

        return $this->render('participant/edit.html.twig', [
            "campus"=> $campus,
            "participant" => $participant,
            'participantForm' => $participantForm->createView()
        ]);

    }

    /**
     * @Route("/participant/create", name="participant_create")
     */
    public function create(Request $request,
                           EntityManagerInterface $entityManager,
                           UpdateEntity $updateEntity
                           //SerieImage $serieImage
                            ): Response
    {

        $isAdmin = $this->isGranted("ROLE_ADMIN");
        if(!$isAdmin){
            throw new AccessDeniedException("Réservé aux admins !");
        }

        // Générer un formulaire pour créer un nouveau participant
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        $participantForm->handleRequest($request);

        // $file = $participantForm->get('poster')->getData();


        if ($participantForm->isSubmitted() && $participantForm->isValid()) { // dans cet ordre là


            //  $file = $participantForm->get('poster')->getData();
            // /**
           //  * @var UploadedFile $file
           //  */
           // if($file){
           //     $directory = $this->getParameter('upload_posters_series_dir');
           //     $serieImage->save($file,$participant,$directory);
           // }

            $updateEntity->save($participant);

            $this->addFlash('success', 'Participant créé !!');

            return $this->redirectToRoute('participant_detail', ['id' => $participant->getId()]);
        }

        return $this->render('participant/create.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }



}
