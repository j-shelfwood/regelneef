<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use OpenAI\Client;

class ChatGPT
{
    const ROLE_USER = 'user';

    const ROLE_SYSTEM = 'system';

    public function __construct(
        protected Client $client,
        protected Collection $messages = collect(),
        protected int $maxTokens = config('openai.max_tokens'),
        protected string $model = config('openai.model'),
        protected float $temperature = config('openai.temperature')
        ) {

    }

    public function messages(): Collection
    {
        return $this->messages;
    }

    public function send(string $message): self
    {
        $this->validateMessageSize($message);

        $this->pushMessage(self::ROLE_USER, $message);

        $this->trimMessagesToFit();

        $message = $this->getChatResponse();

        $this->pushMessage($message['role'], $message['content']);

        return $this;
    }

    public function system(string $message): self
    {
        $this->pushMessage(self::ROLE_SYSTEM, $message);

        return $this;
    }

    public function reset(): self
    {
        $this->messages = collect();

        return $this;
    }

    private function validateMessageSize(string $message): void
    {
        if (OpenAITokenizer::count($message) > $this->maxTokens) {
            throw new MessageTooLargeException('Message is too large');
        }
    }

    private function pushMessage(string $role, string $content): void
    {
        $this->messages->push([
            'role' => $role,
            'content' => $content,
        ]);
    }

    private function trimMessagesToFit(): void
    {
        $totalTokens = OpenAITokenizer::count($this->messages->pluck('content')->implode(' '));

        while ($totalTokens > $this->maxTokens) {
            $this->messages->shift();

            $totalTokens = OpenAITokenizer::count($this->messages);
        }
    }

    private function getChatResponse(): array
    {
        return Cache::rememberForever(md5($this->messages->implode('content')), function () {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'temperature' => $this->temperature,
                'messages' => $this->messages->toArray(),
            ]);

            return [
                'role' => $response->choices[0]->message->role,
                'content' => $response->choices[0]->message->content,
            ];
        });
    }

    public function receive(): string
    {
        // Replace the echo statement with a logger
        // logger('✉️ Response: '.$this->messages->last()['content']);

        return $this->messages->last()['content'];
    }
}
