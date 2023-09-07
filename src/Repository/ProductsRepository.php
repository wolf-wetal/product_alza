<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function save(Products $product)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($product);
        $entityManager->flush();
    }

    public function delete(Products $product)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($product);
        $entityManager->flush();
    }

    public function findAllProducts()
    {
        return $this->findAll();
    }

    public function findProductById($id)
    {
        return $this->find($id);
    }
}
