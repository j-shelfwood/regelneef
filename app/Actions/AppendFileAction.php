<?php

namespace App\Actions;

class AppendFileAction extends Action
{
    public function execute(array $args): string
    {
        $file_path = $args['file_path'];
        $content = $args['content'];

        file_put_contents($file_path, $content, FILE_APPEND);

        return 'Content has been appended to the file.';
    }
}
