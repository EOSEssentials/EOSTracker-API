<?php

namespace AppBundle\Entity;

class Action
{
    private $id;
    private $account;
    private $transaction;
    private $name;
    private $createdAt;
    private $data;
    private $authorizations;
    private $seq;

    public function __construct(int $id, Account $account, Transaction $transaction, string $name, array $data,\DateTime $createdAt, int $seq = 0)
    {
        $this->id = $id;
        $this->account = $account;
        $this->transaction = $transaction;
        $this->name = $name;
        $this->data = $data;
        $this->authorizations = [];
        $this->seq = $seq;
        $this->createdAt = $createdAt;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function seq(): int
    {
        return $this->seq;
    }

    public function account(): Account
    {
        return $this->account;
    }

    public function transaction(): Transaction
    {
        return $this->transaction;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function data(): ?array
    {
        return $this->data;
    }

    public function authorizations()
    {
        return $this->authorizations;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'seq' => $this->seq(),
            'account' => $this->account()->name(),
            'transaction' => $this->transaction()->id(),
            'blockId' => $this->transaction()->blockId(),
            'createdAt' => $this->createdAt()->getTimestamp(),
            'name' => $this->name(),
            'data' => $this->data(),
            'authorizations' => $this->authorizationsToArray()
        ];
    }

    private function authorizationsToArray()
    {
        $authorizations = [];
        foreach($this->authorizations() as $authorization)
        {
            $authorizations[] = $authorization->toArray();
        }

        return $authorizations;
    }
}