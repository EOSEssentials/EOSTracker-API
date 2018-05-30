<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ActionService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 20)
    {
        $query = $this->createQueryBuilder('q')->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return new Paginator($query);
    }
}
