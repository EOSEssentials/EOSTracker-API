<?php

namespace AppBundle\Services;

use AppBundle\Entity\Account;
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
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->useQueryCache(true)
            ->useResultCache(true);

        return new Paginator($query);
    }

    public function producers(\DateTime $since)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT a.name, COUNT(b.producer) as num
FROM AppBundle\Entity\Block b
JOIN b.producer a
WHERE b.timestamp > :since
GROUP BY a
ORDER BY num DESC
DQL
        )
            ->setParameter('since', $since)
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();

        return $query;
    }

    public function withPublicKey(string $publicKey): ?Account
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT a
FROM AppBundle\Entity\Account a
WHERE b.timestamp > :since
GROUP BY a
ORDER BY num DESC
DQL
        )
            ->setParameter(':publicKey', $publicKey)
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();

        return $query;
    }
}
