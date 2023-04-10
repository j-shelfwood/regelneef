<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class GoogleAction extends Action
{
    public function execute(array $args): string
    {
        // Do the google search using the Http facade
        $response = Http::get('https://google.com/search', [
            'q' => $args['query'],
        ]);

        // etc...
    }
}
