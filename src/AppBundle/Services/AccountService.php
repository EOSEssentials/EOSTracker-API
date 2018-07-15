<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;

class AccountService extends EntityRepository
{
    public function get(int $page = 1, int $limit = 30)
    {
        return $this->getEntityManager()->createQuery(<<<DQL
SELECT a
FROM AppBundle\Entity\Account a
ORDER BY a.createdAt DESC
DQL
        )
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->getResult();
    }

    public function producers(\DateTime $since)
    {
        return $this->getEntityManager()->createQuery(<<<DQL
SELECT a.name, COUNT(b.producer) AS num
FROM AppBundle\Entity\BLOCK b
JOIN b.producer a
WHERE b.timestamp > :since
GROUP BY a
ORDER BY num DESC
DQL
        )
            ->setParameter('since', $since)
            ->getResult();
    }

    public function withPublicKey(string $publicKey): ?array
    {
        /*
        $sql = " SELECT account FROM accounts_keys WHERE public_key = '".$publicKey."' LIMIT 1";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute(); */ // TODO: fix
        return [];
    }
}
