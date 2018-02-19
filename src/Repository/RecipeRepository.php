<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findAll() {
        return $this->findBy([], ['id' => 'DESC']);
    }

    public function searchByTitle($title) {
        return $this->createQueryBuilder('x')
           ->where('x.title LIKE :title')
           ->orderBy('x.id', 'DESC')
           ->setParameter('title', '%' . $title . '%')
           ->getQuery()
           ->getResult();
    }
}
