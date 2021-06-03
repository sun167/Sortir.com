<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\SortieSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

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
            ->select('s');
        if (!empty($search->getQ())) {
            $query = $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->getQ()}%");
        }
        if (!empty($search->getCampus())) {
            $query = $query
                ->addSelect('c')
                ->join('s.campus', 'c')
                ->andWhere('c.nom LIKE :nomCampus')
                ->setParameter('nomCampus', "%{$search->getCampus()->getNom()}%");
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
                ->addSelect('e')
                ->join('s.etat', 'e')
                ->andWhere('e.libelle LIKE :passe')
                ->setParameter('passe', "Passée");
        }
        if (!empty($search->isInscrit()) && empty($search->isNonInscrit())) {
            $query = $query
                ->andWhere($query->expr()->isMemberOf(':participants','s.participants'))
                ->setParameter('participants',$search->isInscrit());
        }
        if (!empty($search->isNonInscrit()) && empty($search->isInscrit())) {
            $query = $query
                ->andWhere(':participants NOT MEMBER OF s.participants')
                ->setParameter('participants',$search->isNonInscrit());
        }
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
