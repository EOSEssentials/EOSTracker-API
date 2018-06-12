<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityRepository;

class VoteService extends EntityRepository
{
    public function forProducer(string $producer, int $page = 0): ?array
    {
        $sql = " SELECT v.account, (net+cpu) as staked, votes FROM votes v JOIN stakes s ON s.account = v.account WHERE JSON_CONTAINS(votes, '[\"". $producer ."\"]') ORDER BY staked DESC LIMIT 500 OFFSET ".$page * 500;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
