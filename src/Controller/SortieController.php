<?php

namespace App\Controller;


use App\Entity\SortieSearch;
use App\Form\SortieSearchType;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Upload\SortieImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use App\ManageEntity\UpdateEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{

    /**
     * @Route("/accueil", name="accueil_list")
     */
    public function accueil(Request $request, SortieRepository $sortieRepository): Response
    {
        $isAdmin = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $participant = $this->getUser();
        $sorties = $sortieRepository->findAll();
        $data = new SortieSearch();
        $searchSortieForm = $this->createForm(SortieSearchType::class, $data);
        $searchSortieForm->handleRequest($request);
        //$sorties = $sortieRepository->findSearch($data);
        if (!$sorties) {
            throw $this->createNotFoundException("Sortie inexistant");
        }
        return $this->render('accueil.html.twig', [
            'sorties' => $sorties,
            'particpant' => $participant,
            'form' => $searchSortieForm->createView()
        ]);
    }


    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity, SortieImage $image): Response
    {

        $isAdmin = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        //Création d'une nouvelle sortie
        $participant = $this->getUser();
        $sortie = new Sortie();
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
            //Ajout
            //$updateEntity->save($sortie);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('succes', 'Nouvelle sortie ajoutée !!');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/create.html.twig', ['sortieForm' => $sortieForm->createView(),
            'participant' => $participant
        ]);
    }

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail($id, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager,Request $request): Response
    {
        $isAdmin = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }


        //Détail d'une sortie
        $participant = $this->getUser();
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException("Détail de la sortie inexistant");
        }

        //$etat = $etatRepository->find($id);
        //$updateEtat->ajoutEtat($sortie, $etat);

        $entityManager->persist($sortie);
        //$entityManager->persist($etat);
        $entityManager->flush();

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie,
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("sortie/liste", name="sortie_list")
     */
    public function list(Request $request, SortieRepository $sortieRepository): Response
    {
        $isAdmin = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        //$sorties = $sortieRepository->findAll();
        $participant = $this->getUser();
        $data = new SortieSearch();
        $searchSortieForm = $this->createForm(SortieSearchType::class, $data);
        $searchSortieForm->handleRequest($request);

        $sorties = $sortieRepository->findSearch($data);

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
     * @Route("/ajax-inscription", name="sortie_ajax_inscription")
     */
    public function inscription(Request $request, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent());
        $sortie_id = $data->sortieID;
        $participant_id = $data->participantID;
        $sortie = $sortieRepository->find($sortie_id);
        $participant = $participantRepository->find($participant_id);

        $user = $this->getUser();
        if ($sortie->getParticipants()->contains($user)) {
            $sortie->removeParticipant($participant);
        } else {
            $sortie->addParticipant($participant);
        }
        $entityManager->persist($sortie);
        $entityManager->flush();
        return new JsonResponse([
            'participants' => sizeof($sortie->getParticipants())
        ]);
    }


    /**
     * @Route("/sortie/edit/{id}", name="sortie_edit")
     */
    public function edit($id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        //Modification d'une sortie
        $sortie = $sortieRepository->find($id);
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

        return $this->render('sortie/edit.html.twig', ["sortie" => $sortie, 'sortieForm' => $sortieForm->createView()]);
    }

    /**
     * @Route("/sortie/suppr/{id}", name="sortie_suppr")
     */
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        //Supprimer une sortie
        //TODO PAGE DE RAISON DE SUPPRESSION
        $sortie = $entityManager->find(Sortie::class, $id);
        $entityManager->remove($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('accueil_list');
    }

    /**
     * Retourne un booléen en fonction de si la sortie devrait être archivée
     * @return bool
     */
    public function archivage(Sortie $sortie): bool
    {
        $endDate = new \DateTime("-1 month");
        $date = $sortie->getDateDebut();
        $archive = $sortie->getArchive();
        if($endDate > $date) {
            $archive = 1;
        } else {
            $archive = 0;
        }
        return $archive;
    }
}
