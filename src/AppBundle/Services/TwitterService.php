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

    public function all(int $page = 0, ?int $chainId = null): ?array
    {
        $sql = "SELECT a.id, a.transaction_id, UNIX_TIMESTAMP(created_at) AS created_at, JSON_UNQUOTE(auth->\"$[0].actor\") as actor, JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a WHERE account = 'decentwitter' ". $this->forChain($chainId) ." AND name='tweet' ORDER BY a.id DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function forUser(string $account, int $page = 0, ?int $chainId = null): ?array
    {
        $sql = "SELECT a.id, a.transaction_id, UNIX_TIMESTAMP(created_at) AS created_at, JSON_UNQUOTE(auth->\"$[0].actor\") as actor, JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a WHERE account = 'decentwitter' ". $this->forChain($chainId) ." AND JSON_UNQUOTE(auth->\"$[0].actor\")='".$account."' AND name='tweet' ORDER BY a.id DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function avatarForUser(string $account, ?int $chainId = null): ?string
    {
        $sql = "SELECT JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a WHERE account = 'decentwitter' ". $this->forChain($chainId) ." AND JSON_UNQUOTE(auth->\"$[0].actor\")='".$account."' AND name='avatar' ORDER BY a.id DESC LIMIT 1";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return ($result) ? $result[0]['msg'] : null;
    }

    public function stats(?int $chainId = null): ?array
    {
        $sql = 'SELECT count(a.id) AS amount, DATE(created_at) AS theday FROM actions a WHERE account="decentwitter" '. $this->forChain($chainId) .' AND name="tweet" AND created_at > NOW() - INTERVAL 1 WEEK GROUP BY theday DESC';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function statsForUser(string $account, ?int $chainId = null): ?array
    {
        $sql = 'SELECT count(a.id) AS amount, DATE(created_at) AS theday FROM actions a WHERE a.account="decentwitter" '. $this->forChain($chainId) .' AND a.name="tweet" AND JSON_UNQUOTE(auth->"$[0].actor")="'.$account.'" AND created_at > NOW() - INTERVAL 1 WEEK GROUP BY theday DESC';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    private function forChain(?int $chainId = null, ?string $prefix = ''): string
    {
        return !empty($chainId) ? 'AND '. $prefix. 'chain_id = '. $chainId: '';
    }
}
