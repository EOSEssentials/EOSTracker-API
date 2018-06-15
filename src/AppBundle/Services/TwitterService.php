<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;

class TwitterService extends EntityRepository
{
    public function all(int $page = 0): ?array
    {
        $sql = "SELECT created_at, aa.actor, JSON_UNQUOTE(data->\"$.msg\") as msg FROM actions a JOIN actions_accounts aa ON a.id = aa.action_id JOIN transactions t ON a.transaction_id = t.id WHERE account = 'decentwitter' LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function forUser(string $account, int $page = 0): ?array
    {
        $sql = "SELECT created_at, aa.actor, JSON_UNQUOTE(data->\"$.msg\") as msg FROM actions a JOIN actions_accounts aa ON a.id = aa.action_id JOIN transactions t ON a.transaction_id = t.id WHERE account = 'decentwitter' AND aa.actor='".$account."' LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
