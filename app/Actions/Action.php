<?php

namespace App\Actions;

abstract class Action
{
    abstract public function execute(array $args): string;

    public static function getActionByName(string $name)
    {
        $actionConfig = config("actions.actions.{$name}");

        if (! $actionConfig) {
            throw new \InvalidArgumentException("Action '{$name}' not found.");
        }

        $actionClass = $actionConfig['class'];

        return new $actionClass();
    }
}
