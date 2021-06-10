<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\ManageEntity\UpdateEntity;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville", name="ville")
     */
    public function index(): Response
    {
        return $this->render('ville/index.html.twig', [
            'controller_name' => 'VilleController',
        ]);
    }

    /**
     * @Route("/ville/create", name="ville_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity): Response
    {
        $isAdmin = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $participant = $this->getUser();
        //Création d'une nouvelle ville
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            //Ajout
            $updateEntity->save($ville);
            $this->addFlash('succes', 'Nouvelle ville ajoutée !!');
            return $this->redirectToRoute('ville_detail', ['id' => $ville->getId()]);
        }

        return $this->render('ville/create.html.twig', [
            'villeForm' => $villeForm->createView(),
            'participant' => $participant
        ]);
    }

    /**
     * @Route("/ville/detail/{id}", name="ville_detail")
     */
    public function detail($id, VilleRepository $villeRepository): Response
    {
        $isAdmin = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $participant = $this->getUser();
        $ville = $villeRepository->find($id);
        if (!$ville) {
            throw $this->createNotFoundException("Aucune ville sélectionnée");
        }
        return $this->render('ville/detail.html.twig', [
            'ville' => $ville,
            'participant' => $participant
        ]);
    }

    /**
     * @Route("/ville/list", name="ville_list")
     */
    public function list(VilleRepository $villeRepository): Response
    {
        $isAdmin = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $participant = $this->getUser();
        $ville = $villeRepository->findAll();
        if (!$ville) {
            throw $this->createNotFoundException("Il n'y a pas de ville à afficher");
        }
        return $this->render('ville/list.html.twig', [
            'ville' => $ville,
            'participant' => $participant
        ]);
    }
}
