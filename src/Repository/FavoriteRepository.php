<?php

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\UserFront;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 *
 * @method Favorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favorite[]    findAll()
 * @method Favorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findProduitsPaginatedFavorites(int $page, $searchString, UserFront $user, int $limit = 12): array
    {
        $limit = abs($limit);

        $resultat = [];
        $qb = $this->createQueryBuilder('f')
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
//     * @return Favorite[] Returns an array of Favorite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Favorite
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
