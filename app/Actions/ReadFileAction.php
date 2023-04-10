<?php

namespace App\Actions;

class ReadFileAction extends Action
{
    public function execute(array $args): string
    {
        $file_path = $args['file_path'];

        if (! file_exists($file_path)) {
            return 'File not found.';
        }

        $content = file_get_contents($file_path);

        return "File content:\n{$content}";
    }
}
