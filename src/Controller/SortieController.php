<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\ManageEntity\UpdateEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    /**
     *@Route("/sortie/create", name="sortie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity) : Response {
        //Création d'une nouvelle sortie
        //TODO NE CONTIENT PAS L'IMAGE POUR LE MOMENT
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $updateEntity->save($sortie);
            $this->addFlash('succes', 'Nouvelle sortie ajouter !!');

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', ['sortieForm' => $sortieForm->createView()]);
    }

    /**
     *@Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail($id, SortieRepository $sortieRepository) : Response {
        //Détail d'une sortie
        $sortie = $sortieRepository->find($id);
        if(!$sortie) {
            throw $this->createNotFoundException("Détail de la sortie inexistant");
        }
        return $this->render('sortie/detail.html.twig', ["sortie" => $sortie]);
    }

    //TODO NORMALEMENT C'EST TEMPORAIRE A VOIR AVEC CAO-SON
    /**
     *@Route("/sortie/list", name="sortie_list")
     */
    public function list(SortieRepository $sortieRepository) : Response {
        //Liste des sorties
        $sortie = $sortieRepository->findAll();
        if(!$sortie) {
            throw $this->createNotFoundException("Erreur dans le chargement des listes de sorties");
        }
        return $this->render('sortie/list.html.twig', ["sortie" => $sortie]);
    }

}
