<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TransactionService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 30)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT t
FROM AppBundle\Entity\Transaction t
ORDER BY t.blockId DESC
DQL
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->useQueryCache(true)
            ->useResultCache(true);

        return new Paginator($query);
    }

    public function getForBlock(int $blockNumber, int $page = 1, int $limit = 30)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT t
FROM AppBundle\Entity\Transaction t
WHERE t.blockId = :BLOCKID
ORDER BY t.blockId DESC
DQL
        )
            ->setParameter('BLOCKID', $blockNumber)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->useQueryCache(true)
            ->useResultCache(true);

        return new Paginator($query);
    }
}
