<?php

namespace AppBundle\Services;

use AppBundle\Entity\Account;
use AppBundle\Entity\Transaction;
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
ORDER BY att.blockId DESC
DQL
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return new Paginator($query);
    }

    public function getForAccount(Account $account, int $page = 1, int $limit = 30)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT a, aa, att, ac
FROM AppBundle\Entity\Action a
LEFT JOIN a.authorizations aa
JOIN a.transaction att
JOIN a.account ac
WHERE a.account = :ACCOUNT OR a.id IN (SELECT a2.id FROM AppBundle\Entity\Action a2 JOIN a2.authorizations aa2 WITH aa2.actor = :ACCOUNT)
ORDER BY att.blockId DESC
DQL
        )
            ->setParameter('ACCOUNT', $account)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->useQueryCache(true)
            ->useResultCache(true)
            ->setQueryCacheLifetime(5)
            ->setResultCacheLifetime(5);

        return new Paginator($query);
    }

    public function getForTransaction(Transaction $transaction, int $page = 1, int $limit = 30)
    {
        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT a, aa, att, ac
FROM AppBundle\Entity\Action a
LEFT JOIN a.authorizations aa
JOIN a.transaction att
JOIN a.account ac
WHERE a.transaction = :TRANSACTION
ORDER BY att.blockId DESC
DQL
        )
            ->setParameter('TRANSACTION', $transaction)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->useQueryCache(true)
            ->useResultCache(true)
            ->setQueryCacheLifetime(600)
            ->setResultCacheLifetime(600);

        return new Paginator($query);
    }
}
