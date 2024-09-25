<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advert>
 *
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    //get all published adverts with order by id
    public function allAdvertsQuery()
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = true')
            ->orderBy('a.id', 'ASC')
            ->getQuery();
    }

    //get all published adverts with order by id where title contains $title
    public function titleSearchQuery($title)
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = true')
            ->andWhere('a.title LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->orderBy('a.id', 'ASC')
            ->getQuery();
    }

    //get all published adverts with order by id where title contains $title and category = $category
    public function filteredSearchQuery($title, $category)
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = true')
            ->andWhere('a.title LIKE :title')
            ->andWhere('a.category = :category')
            ->setParameter('title', '%'.$title.'%')
            ->setParameter('category', $category)
            ->orderBy('a.id', 'ASC')
            ->getQuery();
    }

//    /**
//     * @return Advert[] Returns an array of Advert objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Advert
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
