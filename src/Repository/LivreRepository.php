<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 *
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }


// Find/search articles by title/content
public function findArticlesByName(string $query)
{
    $qb = $this->createQueryBuilder('c');
    if ($query) {
        $qb->andWhere('c.nom LIKE :term')
            ->setParameter('term', '%' . $query . '%')
        ;
    }
    return $qb
        ->orderBy('c.id', 'ASC')
        ->getQuery()
        ->getResult()
    ;
}



//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findArticlesByName($query): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.nom = :nom')
//            ->setParameter('nom', $query)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
       
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
