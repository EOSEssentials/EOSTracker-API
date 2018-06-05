<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BlockService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 30)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT b
FROM AppBundle\Entity\Block b
ORDER BY b.blockNumber DESC
DQL
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->useQueryCache(true)
            ->useResultCache(true)
            ->setQueryCacheLifetime(5)
            ->setResultCacheLifetime(5);

        return new Paginator($query);
    }
}
