<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    /**
     * Get all published adverts with category and user eagerly joined (fixes N+1 query problem)
     */
    public function allAdvertsQuery()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c', 'u')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.username', 'u')
            ->where('a.isPublished = true')
            ->orderBy('a.id', 'DESC')
            ->getQuery();
    }

    /**
     * Get search query by title with eager joins
     */
    public function titleSearchQuery($title)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c', 'u')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.username', 'u')
            ->where('a.isPublished = true')
            ->andWhere('a.title LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->orderBy('a.id', 'DESC')
            ->getQuery();
    }

    /**
     * Get filtered search query with eager joins
     */
    public function filteredSearchQuery($title, $category)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c', 'u')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.username', 'u')
            ->where('a.isPublished = true')
            ->andWhere('a.title LIKE :title')
            ->andWhere('a.category = :category')
            ->setParameter('title', '%'.$title.'%')
            ->setParameter('category', $category)
            ->orderBy('a.id', 'DESC')
            ->getQuery();
    }
}
