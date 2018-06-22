<?php

namespace AppBundle\Entity;

class Block
{
    private $id;
    private $blockNumber;
    private $prevBlockId;
    private $irreversible;
    private $timestamp;
    private $transactionMerkleRoot;
    private $actionMerkleRoot;
    private $producer;
    private $numTransactions;
    private $confirmed;
    private $version;
    private $newProducers;

    public function __construct(
        string $id,
        int $blockNumber,
        string $prevBlockId,
        bool $irreversible,
        \DateTime $timestamp,
        string $transactionMerkleRoot,
        string $actionMerkleRoot,
        Account $producer,
        int $numTransactions,
        int $confirmed
    ) {
        $this->id = $id;
        $this->blockNumber = $blockNumber;
        $this->prevBlockId = $prevBlockId;
        $this->irreversible = $irreversible;
        $this->timestamp = $timestamp;
        $this->transactionMerkleRoot = $transactionMerkleRoot;
        $this->actionMerkleRoot = $actionMerkleRoot;
        $this->producer = $producer;
        $this->version = 0;
        $this->numTransactions = $numTransactions;
        $this->confirmed = $confirmed;
        $this->newProducers = [];
    }

    public function id(): string
    {
        return $this->id;
    }

    public function prevBlockId(): string
    {
        return $this->prevBlockId;
    }

    public function blockNumber(): int
    {
        return $this->blockNumber;
    }

    public function newProducers(): array
    {
        return $this->new_producers;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function irreversible(): bool
    {
        return $this->irreversible;
    }

    public function timestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function transactionMerkleRoot(): string
    {
        return $this->transactionMerkleRoot;
    }

    public function actionMerkleRoot(): string
    {
        return $this->actionMerkleRoot;
    }

    public function producer(): Account
    {
        return $this->producer;
    }

    public function numTransactions(): int
    {
        return $this->numTransactions;
    }

    public function confirmed(): int
    {
        return $this->confirmed;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'blockNumber' => $this->blockNumber(),
            'prevBlockId' => $this->prevBlockId(),
            'irreversible' => $this->irreversible(),
            'timestamp' => $this->timestamp()->getTimestamp(),
            'transactionMerkleRoot' => $this->transactionMerkleRoot(),
            'actionMerkleRoot' => $this->actionMerkleRoot(),
            'producer' => $this->producer()->name(),
            'version' => $this->version(),
            'newProducers' => $this->newProducers(),
            'numTransactions' => $this->numTransactions(),
            'confirmed' => $this->confirmed(),
        ];
    }
}