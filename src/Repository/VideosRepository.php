<?php

namespace App\Repository;

use App\Entity\Trick;
use App\Entity\Videos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Videos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videos[]    findAll()
 * @method Videos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Videos::class);
    }

    public function findByVideosTrick(Trick $trick): ?array
    {
        return $this->createQueryBuilder('v')
            ->addSelect('v.name')
            ->addSelect('v.id')
            ->join('v.trick','t')
            ->andWhere('v.id=' . $trick->getId())
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
            ;
    }
}
