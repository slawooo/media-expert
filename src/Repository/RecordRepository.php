<?php

namespace App\Repository;

use App\Dto\RecordSearchCriteriaDto;
use App\Entity\Record;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Record>
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    public function findOneWithStatusHistory(int $id): ?Record
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.statusHistory', 'sh')
            ->addSelect('sh')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->orderBy('sh.createdAt', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Record[]
     */
    public function search(RecordSearchCriteriaDto $criteria): array
    {
        $qb = $this->createQueryBuilder('r')
            ->distinct()
            ->leftJoin('r.statusHistory', 'sh')
            ->addSelect('sh')
            ->orderBy('r.createdAt', 'DESC');

        if ($criteria->number !== null && $criteria->number !== '') {
            $qb
                ->andWhere('r.number LIKE :number')
                ->setParameter('number', '%' . $criteria->number . '%');
        }

        if ($criteria->createdFrom !== null) {
            $qb
                ->andWhere('r.createdAt >= :createdFrom')
                ->setParameter('createdFrom', $criteria->createdFrom);
        }

        if ($criteria->createdTo !== null) {
            $qb
                ->andWhere('r.createdAt <= :createdTo')
                ->setParameter('createdTo', $criteria->createdTo);
        }

        if ($criteria->currentStatus !== null && $criteria->currentStatus !== '') {
            $qb
                ->andWhere('r.currentStatus = :currentStatus')
                ->setParameter('currentStatus', $criteria->currentStatus);
        }

        if ($criteria->historicalStatus !== null && $criteria->historicalStatus !== '') {
            $qb
                ->andWhere('sh.status = :historicalStatus')
                ->setParameter('historicalStatus', $criteria->historicalStatus);
        }

        return $qb->getQuery()->getResult();
    }
}
