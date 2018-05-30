<?php

namespace AppBundle\Entity;

class Transaction
{
    private $id;
    private $blockId;
    private $refBlockPrefix;
    private $expiration;
    private $pending;
    private $numActions;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        string $id,
        int $blockId,
        int $refBlockPrefix,
        \DateTime $expiration,
        bool $pending,
        int $numActions
    ) {
        $this->id = $id;
        $this->blockId = $blockId;
        $this->refBlockPrefix = $refBlockPrefix;
        $this->expiration = $expiration;
        $this->pending = $pending;
        $this->numActions = $numActions;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function blockId(): int
    {
        return $this->blockId;
    }

    public function refBlockPrefix(): int
    {
        return $this->refBlockPrefix;
    }

    public function expiration(): \DateTime
    {
        return $this->expiration;
    }

    public function pending(): bool
    {
        return $this->pending;
    }

    public function numActions(): int
    {
        return $this->numActions;
    }

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'blockId' => $this->blockId(),
            'refBlockPrefix' => $this->refBlockPrefix(),
            'expiration' => $this->expiration()->getTimestamp(),
            'pending' => $this->pending(),
            'numActions' => $this->numActions(),
            'createdAt' => $this->createdAt()->getTimestamp(),
            'updatedAt' => $this->updatedAt()->getTimestamp(),
        ];
    }
}
