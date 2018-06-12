<?php

namespace AppBundle\Entity;

class Vote
{
    private $account;
    private $votes;

    public function __construct(Account $account, array $votes)
    {
        $this->account = $account;
        $this->votes = $votes;
    }

    public function account(): Account
    {
        return $this->account;
    }

    public function votes(): array
    {
        return $this->votes;
    }

    public function toArray(): array
    {
        return [
            'account' => $this->account()->name(),
            'votes' => $this->votes()
        ];
    }
}
