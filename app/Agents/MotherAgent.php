<?php

namespace App\Agents;

class MotherAgent extends Agent
{
    public function activate(): self
    {
        // Set the main agent instructions
        $this->chat->system(get_prompt('mother_agent_instructions'));

        $this->askUserForAgentName()
            ->askForAgentRoleDescription()
            ->askUserForAgentGoals()
            ->start();

        return $this;
    }

    protected function askUserForAgentName(): self
    {
        $this->command->info('💪 Let\'s start by creating a new agent.');
        $this->name = $this->command->ask('What is the name of your agent?', 'ChefGPT');

        $this->prompt .= "Your agent name is {$this->name}. ";

        return $this;
    }

    protected function askForAgentRoleDescription(): self
    {
        $role = $this->command->ask('What is the role of your agent? (e.g. expert programmer, administrative assistant, etc.)', 'An AI designed to browse the web to discover new and unique recipes for upcoming events such as earth day, christmas, etc.');

        $this->prompt .= "Your agent role is $role. ";

        return $this;
    }

    // Define a main goal and up to 5 subgoals
    protected function askUserForAgentGoals(): self
    {
        $this->command->info('💪 Let\'s start by creating a new agent.');

        $mainGoal = $this->command->ask('What is the main goal of your agent?', 'To invent the best recipe for an upcoming event.');

        $this->prompt .= "Your main goal is '$mainGoal'. ";

        // Keep asking subgoals until the user is done
        $subgoals = [];

        do {
            $subgoal = $this->command->ask('What is a subgoal of your agent? (Enter "s" to stop adding subgoals)', 'Finding the next upcomming event, Searching for recipes related to that event for inspiration, Creating a new recipe for the event and saving it to recipe.md file');
            if ($subgoal != 's') {
                $subgoals[] = $subgoal;
            }
        } while ($subgoal != 's' && count($subgoals) < 5);

        $this->prompt .= 'Your subgoals are: '.implode(', ', $subgoals).'. ';

        return $this;
    }
}
