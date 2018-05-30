<?php

namespace AppBundle\Entity;

class Account
{
    private $name;
    private $abi;
    private $createdAt;
    private $updatedAt;

    public function __construct(string $name, array $abi)
    {
        $this->name = $name;
        $this->abi = $abi;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function abi(): ?array
    {
        return $this->abi;
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
            'name' => $this->name(),
            'abi' => $this->abi(),
            'createdAt' => $this->createdAt()->getTimestamp(),
            'updatedAt' => $this->updatedAt()->getTimestamp(),
        ];
    }
}
