<?php

namespace App\Actions;

class WriteFileAction extends Action
{
    public function execute(array $args): string
    {
        $file_path = $args['file_path'];
        $content = $args['content'];

        file_put_contents($file_path, $content);

        return 'Content has been written to the file.';
    }
}
