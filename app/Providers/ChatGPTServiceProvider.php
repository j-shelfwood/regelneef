<?php

namespace App\Providers;

use App\ChatGPT;
use Illuminate\Support\ServiceProvider;
use OpenAI;

class ChatGPTServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->app->bind(ChatGPT::class, function () {
            return new ChatGPT(OpenAI::client(config('openai.api_key')));
        });
    }
}
