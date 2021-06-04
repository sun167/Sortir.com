<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\SortieSearch;
use App\Form\SortieSearchType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Upload\SortieImage;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $participant=$this->getUser();
        $sorties = $sortieRepository->findAll();
        $data = new SortieSearch();
        $searchSortieForm = $this->createForm(SortieSearchType::class, $data);
        $searchSortieForm->handleRequest($request);
        //$sorties = $sortieRepository->findSearch($data);
        if(!$sorties) {
            throw $this->createNotFoundException("Sortie inexistant");
        }
        return $this->render('accueil.html.twig', [
            'sorties' => $sorties,
            'particpant'=>$participant,
            'form' => $searchSortieForm->createView()
        ]);
    }



    /**
     *@Route("/sortie/create", name="sortie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity, SortieImage $image) : Response {

        /*$isAdmin = $this->isGranted("ROLE_ADMIN");
        if(!$isAdmin){
            throw new AccessDeniedException("Réservé aux admins !");
        }*/
        //Création d'une nouvelle sortie
        $participant = $this->getUser();
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
                $newFileName = $sortie->getNom().'-'.uniqid().'.'.$file->guessExtension();
                $file->move($this->getParameter('upload_image_sortie'), $newFileName);
                $sortie->setUrlPhoto($newFileName);
            }
            //Ajout
            $updateEntity->save($sortie);
            $this->addFlash('succes', 'Nouvelle sortie ajoutée !!');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/create.html.twig', ['sortieForm' => $sortieForm->createView(),
            'participant'=>$participant
            ]);
    }

    /**
     *@Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail($id, SortieRepository $sortieRepository) : Response {
        //Détail d'une sortie
        $participant = $this->getUser();
        $sortie = $sortieRepository->find($id);
        if(!$sortie) {
            throw $this->createNotFoundException("Détail de la sortie inexistant");
        }
        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie,
            'participant'=>$participant
        ]);
    }

    /**
     * @Route("sortie/liste", name="sortie_list")
     */
    public function list(Request $request, SortieRepository $sortieRepository): Response
    {
        //$sorties = $sortieRepository->findAll();
        $participant = $this->getUser();
        $data = new SortieSearch();
        $searchSortieForm = $this->createForm(SortieSearchType::class, $data);
        $searchSortieForm->handleRequest($request);

        $sorties = $sortieRepository->findSearch($data);

       if(!$sorties) {
            throw $this->createNotFoundException("Sortie inexistant");
        }
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'participant'=> $participant,
            'form' => $searchSortieForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/detail/ajax-inscription", name="sortie_ajax_inscription")
     */
    public function inscription(Request $request,SortieRepository $sortieRepository,EntityManagerInterface $entityManager): JsonResponse
    {
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
