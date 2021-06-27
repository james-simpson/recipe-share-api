<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findAll($startPage = 1, $count = 20) {
        return $this->createQueryBuilder('x')
            ->setFirstResult($this->getPageOffset($startPage, $count))
            ->setMaxResults($count)
            ->orderBy('x.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUser($userId, $startPage = 1, $count = 20) {
        return $this->createQueryBuilder('x')
            ->where('x.userId = :userId')
            ->setParameter('userId', $userId)
            ->setFirstResult($this->getPageOffset($startPage, $count))
            ->setMaxResults($count)
            ->orderBy('x.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchByTitle($title, $startPage = 1, $count = 20) {
        return $this->createQueryBuilder('x')
           ->where('x.title LIKE :title')
           ->orderBy('x.id', 'DESC')
           ->setParameter('title', '%' . $title . '%')
           ->setFirstResult($this->getPageOffset($startPage, $count))
           ->setMaxResults($count)
           ->getQuery()
           ->getResult();
    }

    public function totalCount() {
        return $this->createQueryBuilder('x')
            ->select('count(x.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByUser($userId) {
        return $this->createQueryBuilder('x')
            ->select('count(x.id)')
            ->where('x.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByTitleSearch($title) {
        return $this->createQueryBuilder('x')
            ->select('count(x.id)')
            ->where('x.title LIKE :title')
            ->setParameter('title', '%' . $title . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getPageOffset($startPage, $count) {
        return ($startPage - 1) * $count;
    }
}
