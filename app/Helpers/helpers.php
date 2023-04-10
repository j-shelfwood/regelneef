<?php

// Gets the path to the storage directory for the application that is used in production.
function app_storage_path(): string
{
    return (getenv('HOME') ?: getenv('USERPROFILE')).DIRECTORY_SEPARATOR.'.regelneef';
}

// Gets a prompt from resources/prompts
function get_prompt(string $name): string
{
    $content = file_get_contents(resource_path("prompts/$name.md"));
    $content = str_replace(
        '@ACTIONS',
        collect(config('actions.actions'))
            ->map(function ($action, $name) {
                // Format: - `@name`: @description (@arguments+description)
                return "- `@$name`: {$action['description']} (".collect($action['arguments'])->map(function ($argument, $name) {
                    return "`$name`: {$argument['description']}";
                })->implode(', ').')';
            })
        ->implode("\n"),
        $content
    );

    return $content;
}
