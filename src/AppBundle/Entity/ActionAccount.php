<?php

namespace AppBundle\Entity;

class ActionAccount
{
    private $action;
    private $actor;
    private $permission;

    public function __construct(Action $action, Account $actor, string $permission)
    {
        $this->action = $action;
        $this->actor = $actor;
        $this->permission = $permission;
    }

    public function actor(): Account
    {
        return $this->actor;
    }

    public function action(): Action
    {
        return $this->action;
    }

    public function permission(): string
    {
        return $this->permission;
    }

    public function toArray(): array
    {
        return [
            'actor' => $this->actor()->name(),
            'action' => $this->action()->id(),
            'permission' => $this->permission(),
        ];
    }
}