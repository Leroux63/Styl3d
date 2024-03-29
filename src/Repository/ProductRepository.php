<?php

namespace App\Repository;

use App\Entity\Images;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getMostAverageRatingProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('p.id, AVG(r.score) as avg_rating')
            ->join('p.ratings', 'r')
            ->groupBy('p.id')
            ->orderBy('avg_rating', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getLastProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }
//    public function getAverageRatingForProduct(): array
//    {
////        return $this->createQueryBuilder('p')
////
////
////            ->addSelect('p.id, AVG(r.score) as avg_rating')
////            ->join('p.ratings','r')
////            ->groupBy('p.id')
////            ->getQuery()
////            ->getResult();
//    }


//    public function getAverageRatingForProduct(int $id): array
//    {
//        return $this->createQueryBuilder('p')
//            ->select('AVG(r.score)')
//            ->join('p.ratings', 'r')
//            ->andWhere('p.id = :id')
//            ->setParameter('id', $id)
//            ->getQuery()
//            ->getResult();
//
//    }
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getAverageRatingForProduct(int $id): array
    {
        return $this->createQueryBuilder('p')
            ->select('AVG(r.score)')
            ->join('p.ratings', 'r')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getTheFirstImageByProduct(): Images
    {
        return $this->createQueryBuilder('p')
            ->addSelect('COUNT(i.id) as HIDDEN nbImage')
            ->join('p.images', 'p')
            ->groupBy('i.id')
            ->orderBy('p.images', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
