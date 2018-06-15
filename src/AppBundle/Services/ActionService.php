<?php

namespace AppBundle\Services;

use AppBundle\Entity\Account;
use AppBundle\Entity\Transaction;
use Doctrine\ORM\EntityRepository;

class ActionService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 30)
    {
        return $this->getEntityManager()->createQuery(<<<DQL
SELECT a, aa, att, acc
FROM AppBundle\Entity\Action a
LEFT JOIN a.authorizations aa
JOIN a.transaction att
JOIN a.account acc
ORDER BY att.blockId DESC
DQL
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getResult();
    }

    public function getFromAccount(Account $account, int $page = 1, int $limit = 30)
    {
        return $this->getEntityManager()->createQuery(<<<DQL
SELECT a, aa, att, ac
FROM AppBundle\Entity\Action a
LEFT JOIN a.authorizations aa
JOIN a.transaction att
JOIN a.account ac
WHERE aa.actor = :ACCOUNT
ORDER BY att.blockId DESC
DQL
        )
            ->setParameter('ACCOUNT', $account)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getResult();
    }

    public function getToAccount(Account $account, int $page = 1, int $limit = 30)
    {
        return $this->getEntityManager()->createQuery(<<<DQL
SELECT a, aa, att, ac
FROM AppBundle\Entity\Action a
LEFT JOIN a.authorizations aa
JOIN a.transaction att
JOIN a.account ac
WHERE a.account = :ACCOUNT
ORDER BY att.blockId DESC
DQL
        )
            ->setParameter('ACCOUNT', $account)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getResult();
    }

    public function getForTransaction(Transaction $transaction, int $page = 1, int $limit = 30)
    {
        return $this->getEntityManager()->createQuery(<<<DQL
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
            ->getResult();
    }
}
