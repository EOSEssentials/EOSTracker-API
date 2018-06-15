<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class TwitterService
{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function all(int $page = 0): ?array
    {
        $sql = "SELECT a.id, created_at, aa.actor, JSON_UNQUOTE(data->\"$.msg\") as msg FROM actions a JOIN actions_accounts aa ON a.id = aa.action_id JOIN transactions t ON a.transaction_id = t.id WHERE account = 'decentwitter' ORDER BY t.created_at DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function forUser(string $account, int $page = 0): ?array
    {
        $sql = "SELECT a.id, created_at, aa.actor, JSON_UNQUOTE(data->\"$.msg\") as msg FROM actions a JOIN actions_accounts aa ON a.id = aa.action_id JOIN transactions t ON a.transaction_id = t.id WHERE account = 'decentwitter' AND aa.actor='".$account."' ORDER BY t.created_at DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
