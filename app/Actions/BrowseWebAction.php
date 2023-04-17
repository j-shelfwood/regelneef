<?php

namespace App\Actions;

use App\ChatGPT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use OpenAI;
use PHPHtmlParser\Dom;

class BrowseWebAction extends Action
{
    public function execute(array $args): string
    {
        $url = $args['url'];
        $goal = $args['goal'];

        $response = Http::get($url)
            ->body();

        // EXTRACT HUMAN READABLE TEXT FROM HTML HERE
        $dom = new Dom();
        $dom->loadStr($response);
        $contentText = '';
        $elements = $dom->find('p, a, h1, h2, h3, h4, h5, h6, li, span, strong, em');
        foreach ($elements as $element) {
            $contentText .= $element->text . ' ';
        }

        // Create a new instance of the ChatGPT class to start summarizing the content through a chat with AI
        $chat = new ChatGPT(OpenAI::client(config('openai.api_key')));

        $chat->system('You are an expert at browsing content of web pages with a specific goal in mind. You extract the most important information from the different chunks of content & summarize it for the user.');

        $chunks = Str::of($contentText)->split(1000)->map(function ($chunk, $index) use ($chat, $goal) {
            // Trim the chunk of any whitespace that could mess up the serialization of the JSON
            $chunk = trim($chunk);

            $chat->send("Using the above text, please answer the following goal/question: {$goal} -- if the question cannot be answered using the text, please summarize the text.");

            $response = $chat->receive();

            // If the response is empty, just return an empty string
            if (Str::of($response)->contains('EMPTY')) {
                return '';
            }

            return $response;
        });

        return $chunks->implode('');
    }
}
