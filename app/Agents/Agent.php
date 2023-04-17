<?php

namespace App\Agents;

use App\Actions\Action;
use App\ChatGPT;
use App\Helpers\OpenAITokenizer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

abstract class Agent
{
    protected ChatGPT $chat;

    protected bool $isContinuous;

    protected Command $command;

    protected string $name;

    protected string $role;

    protected string $prompt;

    public bool $completed = false;

    public function __construct(ChatGPT $chat, Command $command, bool $isContinuous = false)
    {
        $this->chat = $chat;
        $this->command = $command;
        $this->isContinuous = $isContinuous;
        $this->prompt = '';
    }

    abstract public function activate(): self;

    // This starts off the conversation with the Agent and sends the $prompt
    // As configured in this->activate()
    protected function start(): void
    {
        $this->command->info('🛫 Starting up agent...');
        // Get the token counts for $this->chat->messages (content key on each message) and add $this->prompt to it, then output the total token count
        $tokenCount = $this->chat->messages->pluck('content')->map(fn ($message) => OpenAITokenizer::count($message))->sum() + OpenAITokenizer::count($this->prompt);
        $this->command->info("📝 Token count: {$tokenCount}");

        // Send the user message to the AI
        $this->chat->send($this->prompt);
        while (true) {
            // Receive the AI's response
            $response = $this->chat->decode();

            // Display the AI's response
            $this->displayResponse($response);

            if ($response['command']['name'] === 'completed') {
                $this->command->info('🛬 Agent completed.');
                break;
            }

            // Handle user confirmation if $isContinuous is false
            if (!$this->isContinuous) {
                $confirmed = $this->command->confirm('✨ Do you confirm the requested action?');

                if (!$confirmed) {
                    // If not confirmed, send a message to the AI to rethink
                    $message = $this->command->ask('🫵 Provide the agent some guidance on what to do next');
                    $this->chat->send('Action not confirmed. ' . $message);

                    continue;
                }
            }

            // Execute the requested action
            $actionName = $response['command']['name'];
            $actionArgs = $response['command']['args'];

            try {
                $action = Action::getActionByName($actionName, $this->command);
                $actionResult = $action->execute($actionArgs);

                // Send the action result to the AI
                $this->chat->send("The action '{$actionName}' was executed. Result: {$actionResult}");
            } catch (\Exception $e) {
                // If the action fails, send the error message to the AI
                $this->chat->send("The action '{$actionName}' failed. Error: {$e->getMessage()}");
            }
        }

        $this->completed = $response['command']['args']['is_success'];
    }

    protected function displayResponse(Collection $response): void
    {
        // Check if it has the required keys; if not dd() the response
        if (!$response->has(['thoughts', 'command'])) {
            dd($response, $this->chat->messages);
        }
        $this->command->info('💻 [AI]');

        $this->command->info("💭 Thoughts:\n\n{$response['thoughts']['text']}\n");
        $this->command->info("🤔 Reasoning:\n\n{$response['thoughts']['reasoning']}\n");
        $this->command->info("📝 Plan:\n\n{$response['thoughts']['plan']}\n");
        $this->command->info("👎 Criticism:\n\n{$response['thoughts']['criticism']}\n");
        $this->command->info("🗣 Speak:\n\n{$response['thoughts']['speak']}\n");

        $this->command->info('💬 Requested Command : ' . $response['command']['name']);
        $this->command->line('');
        // Show the ['command']['args'] if it's not empty
        if (!empty($response['command']['args'])) {
            $this->command->info('📝 Command Arguments:');
            $this->command->line('');
            foreach ($response['command']['args'] as $key => $value) {
                $this->command->info("{$key}: {$value}");
            }
            $this->command->line('');
        }
    }

    public function isSuccess(): bool
    {
        return $this->completed;
    }
}
