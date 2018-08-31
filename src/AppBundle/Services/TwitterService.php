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
        $sql = "SELECT a.id, a.transaction_id, UNIX_TIMESTAMP(created_at) AS created_at, JSON_UNQUOTE(auth->\"$[0].actor\") as actor, JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a WHERE account = 'decentwitter' AND name='tweet' ORDER BY a.id DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function forUser(string $account, int $page = 0): ?array
    {
        $sql = "SELECT a.id, a.transaction_id, UNIX_TIMESTAMP(created_at) AS created_at, JSON_UNQUOTE(auth->\"$[0].actor\") as actor, JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a WHERE account = 'decentwitter' AND JSON_UNQUOTE(auth->\"$[0].actor\")='".$account."' AND name='tweet' ORDER BY a.id DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function avatarForUser(string $account): ?string
    {
        $sql = "SELECT JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a WHERE account = 'decentwitter' AND JSON_UNQUOTE(auth->\"$[0].actor\")='".$account."' AND name='avatar' ORDER BY a.id DESC LIMIT 1";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return ($result) ? $result[0]['msg'] : null;
    }

    public function stats(): ?array
    {
        $sql = 'SELECT count(a.id) AS amount, DATE(created_at) AS theday FROM actions a WHERE account="decentwitter" AND name="tweet" AND created_at > NOW() - INTERVAL 1 WEEK GROUP BY theday DESC';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function statsForUser(string $account): ?array
    {
        $sql = 'SELECT count(a.id) AS amount, DATE(created_at) AS theday FROM actions a WHERE a.account="decentwitter" AND a.name="tweet" AND JSON_UNQUOTE(auth->"$[0].actor")="'.$account.'" AND t.created_at > NOW() - INTERVAL 1 WEEK GROUP BY theday DESC';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }
}
