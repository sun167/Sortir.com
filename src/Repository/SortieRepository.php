<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\SortieSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

     /**
      * @return Sortie[] Returns an array of searched objects
      */

    public function findSearch(SortieSearch $search)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s', 'e', 'c')
            ->join('s.etat', 'e')
            ->join('s.campus', 'c')
            ->join('s.participant_sortie', 'i');

        if (!empty($search->getQ())) {
            $query = $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->getQ()}%");
        }
        if (!empty($search->getCampus())) {
            $query = $query
                ->andWhere('c.nom LIKE :campus')
                ->setParameter('campus', "%{$search->getCampus()}%");
        }
        if (!(empty($search->getPremierDate()) && empty($search->getDeuxiemeDate()))) {
            $query = $query
                ->andWhere('s.dateDebut > :premierDate')
                ->andWhere('s.dateDebut < :deuxiemeDate')
                ->setParameter('premierDate', $search->getPremierDate())
                ->setParameter('deuxiemeDate', $search->getDeuxiemeDate());
        }
        if (!empty($search->isSortiePassee())) {
            $query = $query
                ->andWhere('e.libelle LIKE :passe')
                ->setParameter('passe', "Passée");
        }
//        if (!empty($search->isInscrit())) {
//            $query = $query
//                ->andWhere('i. LIKE :passe')
//                ->setParameter('passe', "Passée");
//        }
        return $query->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
