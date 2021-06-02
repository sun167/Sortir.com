<?php

namespace App\Controller;

use App\Entity\SortieSearch;
use App\Form\SortieSearchType;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Upload\SortieImage;
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
    public function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity, SortieImage $image) : Response {
        //Création d'une nouvelle sortie
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            //IMAGE
            $file = $sortieForm->get('urlPhoto')->getData();
            /**
             * @var UploadedFile $file
             */
            if($file) {
                $directory = $this->getParameter('upload_img_sortie_dir');
                $image->save($file,$sortie,$directory);
            }

            //Ajout
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

    /**
     * @Route("/sortie/detail/ajax-inscription", name="sortie_ajax_inscription")
     */
    public function inscription(Request $request,SortieRepository $sortieRepository,EntityManagerInterface $entityManager): Response {
        $data = json_decode($request->getContent());
        $sortie_id = $data->sortie_id;
        $inscription = $data->inscription;
        $sortie = $sortieRepository->find($sortie_id);
        if ($inscription == 0) {
            $sortie->setNbDispo($sortie->getNbDispo()+1);
        } else {
            $sortie->setNbDispo($sortie->getNbDispo()-1);
        }
        $entityManager->persist($sortie);
        $entityManager->flush();

        return new JsonResponse(['nbinscription' => $sortie->getNbDispo()]);

    }

}
