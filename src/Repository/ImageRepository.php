<?php

namespace App\Repository;

use App\Entity\Images;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Images|null find($id, $lockMode = null, $lockVersion = null)
 * @method Images|null findOneBy(array $criteria, array $orderBy = null)
 * @method Images[]    findAll()
 * @method Images[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Images::class);
    }

    /**
     * @param \App\Entity\Trick $trick
     * @return array|null Returns an array of Images objects
     */

    public function findImagesByTrick(Trick $trick): ?array
    {
        return $this->createQueryBuilder('i')
            ->addSelect('i.name')
            ->addSelect('i.id')
            ->join('i.trick','t')
            ->andWhere('t.id=' . $trick->getId())
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Images
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
