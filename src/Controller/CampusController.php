<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\ManageEntity\UpdateEntity;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="campus")
     */
    public function index(): Response
    {
        return $this->render('campus/index.html.twig', [
            'controller_name' => 'CampusController',
        ]);
    }

    /**
     * @Route("/campus/create", name="campus_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity): Response
    {
        //Création d'un nouveau campus
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()) {
            //Ajout
            $updateEntity->save($campus);
            $this->addFlash('succes', 'Nouveau campus ajouter !!');
            return $this->redirectToRoute('campus_detail', ['id' => $campus->getId()]);
        }

        return $this->render('campus/create.html.twig', ['campusForm' => $campusForm->createView()]);
    }

    /**
     * @Route("/campus/detail/{id}", name="campus_detail")
     */
    public function detail($id, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);
        if(!$campus) {
            throw $this->createNotFoundException("Détail du campus inexistant");
        }
        return $this->render('campus/detail.html.twig', [
            'campus' => $campus
        ]);
    }

    /**
     * @Route("/campus/list", name="campus_list")
     */
    public function list(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();
        if(!$campus) {
            throw $this->createNotFoundException("Erreur dans le chargement des listes des campus");
        }
        return $this->render('campus/list.html.twig', [
            'campus' => $campus
        ]);
    }
}
