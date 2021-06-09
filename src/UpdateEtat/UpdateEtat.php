<?php
/*

namespace App\UpdateEtat;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;


class UpdateEtat extends Etat
{

    public function __construct(EtatRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function ajoutEtat(Sortie $sortie, Etat $etat): Etat
    {
        $dateDebut = $sortie->getDateDebut();
        $dateFin =$sortie->getDateFin();
        $dateNow = new \DateTime('now');



        if($dateNow == $dateDebut) {
            $etat->setLibelle('Activitée en cours');
        }
        if($dateNow > $dateDebut) {
            $etat->setLibelle('Passée');
        }
        if($dateNow < $dateDebut && $dateNow > $dateFin) {
            $etat->setLibelle('Cloturée');
        }



        $etat->setLibelle('CA ME SOULE !!!!!!!!');
        $etat = $sortie->getEtat();
        return $etat;
    }

}
*/