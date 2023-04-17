<?php

namespace App;

use App\Helpers\OpenAITokenizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use OpenAI\Client;

class ChatGPT
{
    const ROLE_USER = 'user';

    const ROLE_SYSTEM = 'system';

    public Collection $messages;

    protected Client $client;

    protected int $maxTokens;

    protected string $model;

    protected float $temperature;

    public function __construct(Client $client, ?Collection $messages = null)
    {
        if (is_null($messages)) {
            $messages = collect();
        }
        $this->messages = $messages;
        $this->client = $client;
        $this->maxTokens = config('openai.max_tokens');
        $this->model = config('openai.model');
        $this->temperature = config('openai.temperature');
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
            $overflow = $totalTokens - $this->maxTokens;
            echo PHP_EOL . '⚠️ Trimming message (' . $overflow . ' tokens over context limit)' . PHP_EOL;
            $this->messages = $this->messages->slice(1)->values();

            $totalTokens = OpenAITokenizer::count($this->messages);
        }
    }

    private function getChatResponse(): array
    {
        return Cache::rememberForever(md5($this->messages->implode('content')), function () {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'temperature' => $this->temperature,
                'messages' => (array) $this->messages->toArray(),
            ]);

            return [
                'role' => $response->choices[0]->message->role,
                'content' => $response->choices[0]->message->content,
            ];
        });
    }

    public function receive(): string
    {
        return $this->messages->last()['content'];
    }

    public function decode(): Collection
    {
        $content = $this->messages->last()['content'];

        // If the string does not start with a {, it's not JSON, try to slice it off
        if (!str_starts_with($this->messages->last()['content'], '{')) {
            $content = substr($this->messages->last()['content'], strpos($this->messages->last()['content'], '{'));
        }

        if (!json_decode($this->messages->last()['content'], true)) {
            echo PHP_EOL . '⚠️ Could not decode message: ';
            echo $content;
        }

        return collect(json_decode($content, true));
    }
}
