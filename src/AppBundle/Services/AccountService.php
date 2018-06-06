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
FROM AppBundle\Entity\ACCOUNT a
ORDER BY a.createdAt DESC
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

    public function producers(\DateTime $since)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT a.name, COUNT(b.producer) AS num
FROM AppBundle\Entity\BLOCK b
JOIN b.producer a
WHERE b.timestamp > :since
GROUP BY a
ORDER BY num DESC
DQL
        )
            ->setParameter('since', $since)
            ->useQueryCache(true)
            ->useResultCache(true)
            ->setQueryCacheLifetime(600)
            ->setResultCacheLifetime(600)
            ->getResult();

        return $query;
    }

    public function withPublicKey(string $publicKey): ?array
    {
        $sql = " SELECT account FROM accounts_keys WHERE public_key = '".$publicKey."' LIMIT 1";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
