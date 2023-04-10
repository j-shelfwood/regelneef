<?php

namespace App\Actions;

class CompletedAction extends Action
{
    public function execute(array $args): string
    {
        $status = $args['status'];
        $is_success = $args['is_success'];

        // Shut down the agent (assuming it's a separate process)
        // exec("kill <agent_pid>");

        return "Status: {$status}\nSuccess: ".($is_success ? 'Yes' : 'No');
    }
}
