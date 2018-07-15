<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class VoteService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function forProducer(string $producer, int $page = 0): ?array
    {
        return [];
    }
}
