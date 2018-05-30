<?php

namespace AppBundle\Entity;

class Action
{
    private $id;
    private $account;
    private $transaction;
    private $name;
    private $data;

    public function __construct(string $id, Account $account, Transaction $transaction, string $name, array $data)
    {
        $this->id = $id;
        $this->account = $account;
        $this->transaction = $transaction;
        $this->name = $name;
        $this->data = $data;
    }

    public function id(): string
    {
        return $this->id;
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

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'account' => $this->account()->name(),
            'transaction' => $this->transaction()->id(),
            'name' => $this->name(),
            'data' => $this->data(),
        ];
    }
}