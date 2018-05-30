<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TransactionService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 20)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT t
FROM AppBundle\Entity\Transaction t
ORDER BY t.createdAt DESC
DQL
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return new Paginator($query);
    }
}
