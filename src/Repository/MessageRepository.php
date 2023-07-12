<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function queryAllByTrick(Trick $trick)
    {
        return $this->createQueryBuilder('m')
            ->where('m.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();
    }
}
