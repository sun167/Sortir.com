<?php

namespace App\Controller;

use App\Entity\SortieSearch;
use App\Form\SortieSearchType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\ManageEntity\UpdateEntity;
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

    /**
     * @Route("/list", name="sortie_list")
     */
    public function list(Request $request, SortieRepository $sortieRepository): Response
    {
        $data = new SortieSearch();
        $searchSortieForm = $this->createForm(SortieSearchType::class, $data);
        $searchSortieForm->handleRequest($request);
        $sorties = $sortieRepository->findSearch($data);
        if(!$sorties) {
            throw $this->createNotFoundException("Sortie inexistant");
        }

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'form' => $searchSortieForm->createView()

        ]);
    }

}
