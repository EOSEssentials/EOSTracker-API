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
        $sql = "SELECT a.id, a.transaction_id, a.seq, UNIX_TIMESTAMP(t.created_at) AS created_at, aa.actor, JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a JOIN transactions t ON a.transaction_id = t.id JOIN actions_accounts aa ON a.id = aa.action_id WHERE account = 'decentwitter' AND name='tweet' ORDER BY a.id DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function replies(array $tweetIds): ?array
    {
        $sql = "SELECT a.id, a.transaction_id, a.seq, UNIX_TIMESTAMP(t.created_at) AS created_at, aa.actor, JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a JOIN transactions t ON a.transaction_id = t.id JOIN actions_accounts aa ON a.id = aa.action_id WHERE account = 'decentwitter' AND name='tweet' ORDER BY a.id DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function forUser(string $account, int $page = 0): ?array
    {
        $sql = "SELECT a.id, a.transaction_id, a.seq, UNIX_TIMESTAMP(t.created_at) AS created_at, aa.actor, JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a JOIN transactions t ON a.transaction_id = t.id JOIN actions_accounts aa ON a.id = aa.action_id WHERE account = 'decentwitter' AND aa.actor='".$account."' AND name='tweet' ORDER BY a.id DESC LIMIT 50 OFFSET ".$page * 50;
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function avatarForUser(string $account): ?string
    {
        $sql = "SELECT JSON_UNQUOTE(data->\"$.msg\") AS msg FROM actions a JOIN actions_accounts aa ON a.id = aa.action_id WHERE account = 'decentwitter' AND aa.actor='".$account."' AND name='avatar' ORDER BY a.id DESC LIMIT 1";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return ($result) ? $result[0]['msg'] : null;
    }

    public function stats(): ?array
    {
        $sql = 'SELECT count(a.id) AS amount, DATE(t.created_at) AS theday FROM actions a JOIN transactions t ON a.transaction_id = t.id WHERE account="decentwitter" AND name="tweet" GROUP BY theday DESC';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function statsForUser(string $account): ?array
    {
        $sql = 'SELECT count(a.id) AS amount, DATE(t.created_at) AS theday FROM actions a JOIN transactions t ON a.transaction_id = t.id JOIN actions_accounts aa ON a.id = aa.action_id WHERE a.account="decentwitter" AND a.name="tweet" AND aa.actor="'.$account.'" GROUP BY theday DESC';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }
}
