<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ActionService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 30)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT a, aa
FROM AppBundle\Entity\Action a
LEFT JOIN a.authorizations aa
JOIN a.transaction att
ORDER BY att.createdAt DESC
DQL
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return new Paginator($query);
    }
}
