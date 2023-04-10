<?php

namespace App\Actions;

use DOMDocument;
use Illuminate\Support\Facades\Http;

class GoogleAction extends Action
{
    public function execute(array $args): string
    {
        $query = $args['query'];

        // Do the google search using the Http facade
        $response = Http::get('https://google.com/search', [
            'q' => $query,
        ]);

        // We want to send the user 6 results
        $results = [];

        // We'll use the DOMDocument class to parse the HTML
        $dom = new DOMDocument();
        @$dom->loadHTML($response->body());

        // Get all the links
        $links = $dom->getElementsByTagName('a');

        // Loop through the links
        foreach ($links as $link) {
            // Get the href attribute
            $href = $link->getAttribute('href');
        }

        // Return the results
        return implode(PHP_EOL, $results);
    }
}
