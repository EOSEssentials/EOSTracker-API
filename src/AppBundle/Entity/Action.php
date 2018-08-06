<?php

namespace AppBundle\Entity;

class Action
{
    private $id;
    private $account;
    private $transaction;
    private $name;
    private $data;
    private $authorizations;
    private $seq;
    private $parentId;

    public function __construct(int $id, string $account, Transaction $transaction, string $name, array $data, int $seq = 0, int $parentId = 0)
    {
        $this->id = $id;
        $this->account = $account;
        $this->transaction = $transaction;
        $this->name = $name;
        $this->data = $data;
        $this->authorizations = [];
        $this->seq = $seq;
        $this->parentId = $parentId;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function parentId(): int
    {
        return $this->parentId;
    }

    public function seq(): int
    {
        return $this->seq;
    }

    public function account(): string
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

    public function data()
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
            'account' => $this->account(),
            'transaction' => $this->transaction()->id(),
            'blockId' => $this->transaction()->blockId(),
            'parentId' => $this->parentId(),
            'createdAt' => $this->transaction()->createdAt()->getTimestamp(),
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