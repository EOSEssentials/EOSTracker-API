<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AccountService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 30)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT a
FROM AppBundle\Entity\Account a
ORDER BY a.createdAt DESC
DQL
        )            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return new Paginator($query);
    }
}
