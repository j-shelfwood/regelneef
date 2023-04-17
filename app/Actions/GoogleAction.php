<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class GoogleAction extends Action
{
    public function execute(array $args): string
    {
        $query = $args['query'];

        // Do the google search using the official google api
        $response = Http::get('https://www.googleapis.com/customsearch/v1', [
            'key' => config('google.api_key'),
            'cx' => config('google.search_engine_id'),
            'q' => $query,
        ]);

        $items = $response->collect()->get('items');

        // Format the results
        $results = collect($items)->map(function ($result) {
            return "{$result['title']} - {$result['snippet']} - {$result['link']}";
        });

        // Return the results
        return $results->toJson();
    }
}
