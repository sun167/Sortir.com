<?php

namespace App\Controller;

use App\Command\ChangeStateSortieCommand;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\AnnulerSortieType;
use App\Form\LieuType;
use App\Entity\SortieSearch;
use App\Form\SortieSearchType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Form\VilleType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use App\Upload\SortieImage;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use App\ManageEntity\UpdateEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class SortieController extends AbstractController
{

    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public function create(Request $request, UserInterface $userInterface, UpdateEntity $updateEntity, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {

        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        //Création d'une nouvelle sortie

        $participant = $this->getUser();

        $sortie = new Sortie();

        //$participant_id = (int)$request->query->get('participantID');
        //$participant = $participantRepository->find($participant_id);
        dump($participant);
        $sortie->setOrganisateur($participant);

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            //IMAGE
            $file = $sortieForm->get('urlPhoto')->getData();
            /**
             * @var UploadedFile $file
             */
            if ($file) {
                $newFileName = $sortie->getNom() . '-' . uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_image_sortie'), $newFileName);
                $sortie->setUrlPhoto($newFileName);
            }

            //Logique pour différencier les deux boutons enregistrer et publier
            if($sortieForm->get('enregistrer')->isClicked()){
                $etat = $etatRepository->find(1);
            }else{
                $etat = $etatRepository->find(2);
            }
            $sortie->setEtat($etat);

            //Ajout
            // $updateEntity->save($sortie);
            $entityManager->persist($sortie);
            $entityManager->flush();



            $this->addFlash('succes', 'Nouvelle sortie ajoutée !!');
            return $this->redirectToRoute('sortie_list', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/create.html.twig', ['sortieForm' => $sortieForm->createView(),
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail($id, SortieRepository $sortieRepository, LieuRepository $lieuRepository, VilleRepository $villeRepository): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }


        //Détail d'une sortie
        $participant = $this->getUser();
        $sortie = $sortieRepository->find($id);
        $lieu = $lieuRepository->find($id);
        $ville = $villeRepository->find($id);
        dump($ville);
        if (!$sortie) {
            throw $this->createNotFoundException("Détail de la sortie inexistant");
        }
        return $this->render('sortie/detail.html.twig', [
            "ville"=>$ville,
            'lieu'=>$lieu,
            "sortie" => $sortie,
            'participant' => $participant
        ]);
    }

    /**
     * @Route("sortie/liste", name="sortie_list")
     */
    public function list(Request $request, SortieRepository $sortieRepository, ChangeStateSortieCommand $changeStateSortieCommand): Response
    {
        $participant = $this->getUser();
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $data = new SortieSearch();
        $searchSortieForm = $this->createForm(SortieSearchType::class, $data);
        $searchSortieForm->handleRequest($request);

        $sorties = $sortieRepository->findSearch($data, $participant);

        if (!$sorties) {
            throw $this->createNotFoundException("Sortie inexistant");
        }
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'participant' => $participant,
            'form' => $searchSortieForm->createView()
        ]);
    }

    /**
     * @Route("sortie/ajax-inscription", name="sortie_ajax_inscription")
     */
    public function inscription(Request $request, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {

        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $sortie_id = (int)$request->query->get('sortieID');
        $participant_id = (int)$request->query->get('participantID');
        $sortie = $sortieRepository->find($sortie_id);
        $participant = $participantRepository->find($participant_id);
        $sortie->addParticipant($participant);

        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->render("ajax/inscription.html.twig", [
            //je veux le nombre de participant
            "nbInscrit" => $sortie->getParticipants()->count(),
            "sortie" => $sortie
        ]);
    }


    /**
     * @Route("sortie/ajax-desister", name="sortie_ajax_desister")
     */
    public function desister(Request $request, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        $sortie_id = (int)$request->query->get('sortieID');
        $participant_id = (int)$request->query->get('participantID');
        $sortie = $sortieRepository->find($sortie_id);
        $participant = $participantRepository->find($participant_id);
        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->render("ajax/inscription.html.twig", [
            "nbInscrit" => $sortie->getParticipants()->count(),
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/sortie/edit/{id}", name="sortie_edit")
     */
    public function edit($id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request, LieuRepository  $lieuRepository, VilleRepository $villeRepository): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }



        //Modification d'une sortie
        $sortie = $sortieRepository->find($id);
        $lieu = $lieuRepository->find($id);
        $ville = $villeRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException("Détail de la sortie inexistant");
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'Sortie modifier !!');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/edit.html.twig', [
            "sortie" => $sortie,
            'sortieForm' => $sortieForm->createView(),
            'ville' => $ville,
            'lieu' => $lieu
        ]);
    }

    /**
     * @Route("/sortie/suppr/{id}", name="sortie_suppr")
     */
    public function delete($id, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, Request $request, EtatRepository $etatRepository, LieuRepository $lieuRepository, VilleRepository $villeRepository): Response
    {
        //Annuler une nouvelle sortie
        $participant = $this->getUser();

        $sortie = $sortieRepository->find($id);
        $lieu = $lieuRepository->find($id);
        $ville = $villeRepository->find($id);
        dump($ville);
        if (!$sortie) {
            throw $this->createNotFoundException("Détail de la sortie inexistant");
        }
        $annulerForm = $this->createForm(AnnulerSortieType::class, $sortie);
        $annulerForm->handleRequest($request);

        if ($annulerForm->isSubmitted() && $annulerForm->isValid()) {

            //changé l'état en annulée

            $etat = $etatRepository->find(6);
            $sortie->setEtat($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'Sortie annulée!!');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/annuler.html.twig', [
            'annulerForm' => $annulerForm->createView(),
            "sortie" => $sortie,
            'participant' => $participant,
            'lieu'=>$lieu,
            'ville'=>$ville]);
    }


}
