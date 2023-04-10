<?php

namespace App\Actions;

use App\Helpers\OpenAITokenizer;
use Symfony\Component\DomCrawler\Crawler;

class BrowseWebAction extends Action
{
    public function execute(array $args): string
    {
        $url = $args['url'];
        $goal = $args['goal'];

        // Use the Symfony Crawer to browse the web
        $crawler = new Crawler(file_get_contents($url));

        // Get the text of the page
        $result = $crawler->text();
        $tokenCount = OpenAITokenizer::count($result);

        echo "Found {$tokenCount} tokens on the page.";

        return "Found information: {$result}";
    }
}
