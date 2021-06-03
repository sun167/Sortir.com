<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\ManageEntity\UpdateEntity;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu", name="lieu")
     */
    public function index(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    /**
     * @Route("/lieu/create", name="lieu_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity): Response
    {
        //Création d'un nouveau lieu
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            //Ajout
            $updateEntity->save($lieu);
            $this->addFlash('succes', 'Nouveau lieu ajouter !!');
            return $this->redirectToRoute('lieu_detail', ['id' => $lieu->getId()]);
        }

        return $this->render('lieu/create.html.twig', ['lieuForm' => $lieuForm->createView()]);
    }


    /**
     * @Route("/lieu/detail/{id}", name="lieu_detail")
     */
    public function detail($id, LieuRepository $lieuRepository): Response
    {
        $lieu = $lieuRepository->find($id);
        if(!$lieu) {
            throw $this->createNotFoundException("Détail du lieu inexistant");
        }
        return $this->render('lieu/detail.html.twig', [
            'lieu' => $lieu
        ]);
    }

    /**
     * @Route("/lieu/list", name="lieu_list")
     */
    public function list(LieuRepository $lieuRepository): Response
    {
        $lieu = $lieuRepository->findAll();
        if(!$lieu) {
            throw $this->createNotFoundException("Erreur dans le chargement des listes des lieux");
        }
        return $this->render('lieu/list.html.twig', [
            'lieu' => $lieu
        ]);
    }
}
