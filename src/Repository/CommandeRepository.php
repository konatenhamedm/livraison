<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    //private $user;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
        // $this->user = $user;
    }

    public function save(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findCommandeUserPaginated(int $page, string $user, int $limit = 12): array
    {

        // dd($this->user);
        $limit = abs($limit);

        $resultat = [];
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.utilisateur', 'u')
            ->andWhere('u.email = :user')
            ->setParameter('user', $user)
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($qb);
        $data = $paginator->getQuery()->getResult();

        // if (empty($data)) {
        //     return $resultat;
        // }

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
    //     * @return Commande[] Returns an array of Commande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
