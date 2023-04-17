<?php

namespace App\Actions;

use Illuminate\Console\Command;

abstract class Action
{
    protected Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    abstract public function execute(array $args): string;

    public static function getActionByName(string $name, Command $command): Action
    {
        $actionConfig = config("actions.actions.{$name}");

        if (! $actionConfig) {
            throw new \InvalidArgumentException("Action '{$name}' not found.");
        }

        $actionClass = $actionConfig['class'];

        return new $actionClass($command);
    }
}
