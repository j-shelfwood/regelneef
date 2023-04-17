<?php

return [
    'actions' => [
        'completed' => [
            'description' => 'Shut down the agent and return the status of the goals',
            'class' => App\Actions\CompletedAction::class,
            'arguments' => [
                'status' => [
                    'description' => 'The status of the goals',
                ],
                'is_success' => [
                    'description' => 'Whether the goals were completed successfully',
                ],
            ],
        ],
        'browse_web' => [
            'description' => 'Summarize a web page with a specific goal in mind',
            'class' => App\Actions\BrowseWebAction::class,
            'arguments' => [
                'url' => [
                    'description' => 'The URL to browse',
                ],
                'goal' => [
                    'description' => 'What do you want to find on the page?',
                ],
            ],
        ],
        'google' => [
            'description' => 'Search Google for websites to browse',
            'class' => App\Actions\GoogleAction::class,
            'arguments' => [
                'query' => [
                    'description' => 'The query to search for',
                ],
            ],
        ],
        'read_file' => [
            'description' => 'Read content from a file',
            'class' => App\Actions\ReadFileAction::class,
            'arguments' => [
                'file_path' => [
                    'description' => 'The absolute file path to read',
                ],
            ],
        ],
        'write_file' => [
            'description' => 'Write content to a file',
            'class' => App\Actions\WriteFileAction::class,
            'arguments' => [
                'file_path' => [
                    'description' => 'The absolute file path to write to',
                ],
                'content' => [
                    'description' => 'The content to write to the file',
                ],
            ],
        ],
        'append_file' => [
            'description' => 'Append content to a file',
            'class' => App\Actions\AppendFileAction::class,
            'arguments' => [
                'file_path' => [
                    'description' => 'The absolute file path to append to',
                ],
                'content' => [
                    'description' => 'The content to append to the file ',
                ],
            ],
        ],
        'delete_file' => [
            'description' => 'Delete a file',
            'class' => App\Actions\DeleteFileAction::class,
            'arguments' => [
                'file_path' => [
                    'description' => 'The absolute file path to delete (current working directory and down only)',
                ],
            ],
        ],
        'search_files' => [
            'description' => 'Search for files',
            'class' => App\Actions\SearchFilesAction::class,
            'arguments' => [
                'query' => [
                    'description' => 'The regex to search by filename (current working director and down only)',
                ],
            ],
        ],
    ],
];
