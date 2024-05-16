<?php

namespace App\Repository;

use App\Entity\Versement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Versement>
 *
 * @method Versement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Versement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Versement[]    findAll()
 * @method Versement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Versement::class);
    }

    public function searchResult($dateDebut, $dateFin)
    {
        $sql = $this->createQueryBuilder('i')
            ->leftJoin('i.commande', 'p');

        if ($dateDebut || $dateFin) {


            //dd($dateDebut);

            if ($dateDebut != "null" && $dateFin == "null") {
                $sql->andWhere('i.datePaiement = :dateDebut')
                    ->setParameter('dateDebut', $dateDebut);
            }
            if ($dateFin != "null" && $dateDebut == "null") {
                $sql->andWhere('i.datePaiement  = :dateFin')
                    ->setParameter('dateFin', $dateFin);
            }
            if ($dateDebut != "null" && $dateFin != "null") {
                $sql->andWhere('i.datePaiement BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter("dateFin", $dateFin);
            }
        }

        return $sql->getQuery()->getResult();
    }

    //    /**
    //     * @return Versement[] Returns an array of Versement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Versement
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
