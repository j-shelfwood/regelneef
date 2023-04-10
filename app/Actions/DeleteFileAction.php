<?php

namespace App\Actions;

class DeleteFileAction extends Action
{
    public function execute(array $args): string
    {
        $file_path = $args['file_path'];

        if (! file_exists($file_path)) {
            return 'File not found.';
        }

        unlink($file_path);

        return 'File has been deleted.';
    }
}
