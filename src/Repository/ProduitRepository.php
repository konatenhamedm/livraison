<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findProduitsPaginated(int $page, int $id, int $limit = 12): array
    {
        $limit = abs($limit);

        $resultat = [];
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.categorie', 'c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($qb);
        $data = $paginator->getQuery()->getResult();

        if (empty($data)) {
            return $resultat;
        }

        $pages = ceil($paginator->count() / $limit);
        $resultat = [
            'data' => $data,
            'pages' => $pages,
            'page' => $page,
            'limit' => $limit
        ];
        return $resultat;
    }
    public function findProduitsPaginatedAllProduct(int $page, $searchString, int $limit = 12): array
    {
        $limit = abs($limit);

        $resultat = [];
        $qb = $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        if ($searchString != '') {
            $qb->andWhere('p.libelle LIKE :search')
                ->setParameter('search', '%' . $searchString . '%');
        }

        $paginator = new Paginator($qb);
        $data = $paginator->getQuery()->getResult();

        if (empty($data)) {
            return $resultat;
        }

        $pages = ceil($paginator->count() / $limit);
        $resultat = [
            'data' => $data,
            'pages' => $pages,
            'page' => $page,
            'limit' => $limit
        ];
        return $resultat;
    }


    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
