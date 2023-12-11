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
            ->setParameter('term', '%' . $query . '%');
    }
    return $qb
        ->orderBy('c.id', 'ASC')
        ->getQuery()
        ->getResult()
    ;
}

public function findArticlesByAuteur(string $query)
{
    $qb = $this->createQueryBuilder('c');
    if ($query) {
        $qb->andWhere('c.auteur = :auteur')
            ->setParameter('auteur',$query);
    }
    return $qb
        ->orderBy('c.auteur', 'ASC')
        ->getQuery()
        ->getResult()
    ;
}
public function findArticlesByEditeur(string $query)
{
    $qb = $this->createQueryBuilder('c');
    if ($query) {
        $qb->andWhere('c.editeur = :editeur')
            ->setParameter('editeur',$query);
    }
    return $qb
        ->orderBy('c.editeur', 'ASC')
        ->getQuery()
        ->getResult()
    ;
}
public function findArticlesByGenre(string $query)
{
    $qb = $this->createQueryBuilder('c');
    if ($query) {
        $qb->andWhere('c.genre = :genre')
            ->setParameter('genre',$query);
    }
    return $qb
        ->orderBy('c.genre', 'ASC')
        ->getQuery()
        ->getResult()
    ;
}

public function findArticlesByAll($query, $query2, $query3)
{
    if((($query == "") && ($query2 == ""))) {
        return $this->createQueryBuilder("a")
        ->Where('a.genre = :genre')
        ->setParameter('genre',$query3)
        ->getQuery()
        ->getResult()
    ;
    } else if ((($query2 == "") && ($query3 == ""))) {
        return $this->createQueryBuilder("a")
        ->Where('a.auteur = :auteur')
        ->setParameter('auteur',$query)
        ->getQuery()
        ->getResult()
    ;
    } else if ((($query == "") && ($query3 == ""))) {
        return $this->createQueryBuilder("a")
        ->Where('a.editeur = :editeur')
        ->setParameter('editeur',$query2)
        ->getQuery()
        ->getResult()
    ;
    } else if ($query == "") {
        return $this->createQueryBuilder("a")
        ->Where('a.editeur = :editeur')
        ->andWhere('a.genre = :genre')
        ->setParameter('editeur',$query2)
        ->setParameter('genre',$query3)
        ->getQuery()
        ->getResult()
    ;
    } else if ($query2 == "") {
        return $this->createQueryBuilder("a")
        ->Where('a.auteur = :auteur')
        ->andWhere('a.genre = :genre')
        ->setParameter('auteur',$query)
        ->setParameter('genre',$query3)
        ->getQuery()
        ->getResult()
    ;
    } else if ($query3 == "") {
        return $this->createQueryBuilder("a")
        ->Where('a.auteur = :auteur')
        ->andWhere('a.editeur = :editeur')
        ->setParameter('auteur',$query)
        ->setParameter('editeur',$query2)
        ->getQuery()
        ->getResult()
    ;
    } else {return $this->createQueryBuilder("a")
        ->where('a.auteur = :auteur')
        ->andWhere('a.editeur = :editeur')
        ->andWhere('a.genre = :genre')
        ->setParameter('auteur',$query)
        ->setParameter('editeur',$query2)
        ->setParameter('genre',$query3)
        ->getQuery()
        ->getResult()
    ;
}
}
}