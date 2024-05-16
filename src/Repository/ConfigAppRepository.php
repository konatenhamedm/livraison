<?php

namespace App\Repository;

use App\Entity\ConfigApp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<ConfigApp>
 *
 * @method ConfigApp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigApp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigApp[]    findAll()
 * @method ConfigApp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigAppRepository extends ServiceEntityRepository
{
    protected $entreprise;
    protected $user;
    public function __construct(ManagerRegistry $registry, Security $security,)
    {
        parent::__construct($registry, ConfigApp::class);
        if ($security->getUser() != null) {
            $this->entreprise = $security->getUser()->getEmploye()->getEntreprise()->getCode();
        } else {
            $this->entreprise = 'ENT1';
        }
        $this->user = $security->getUser();
        // dd($this->entreprise);
    }

    public function save(ConfigApp $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConfigApp $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findConfig()
    {
        return   $this->createQueryBuilder('c')
            ->innerJoin('c.entreprise', 'e')
            ->where('e.code = :entreprise')
            ->setParameter('entreprise', $this->entreprise)
            ->getQuery()
            ->getOneOrNullResult();
    }
    //    /**
    //     * @return ConfigApp[] Returns an array of ConfigApp objects
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

    //    public function findOneBySomeField($value): ?ConfigApp
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
