<?php

namespace App\Actions;

class SearchFilesAction extends Action
{
    public function execute(array $args): string
    {
        $query = $args['query'];
        $current_dir = getcwd();
        $files = glob("{$current_dir}/*");

        $matched_files = [];
        foreach ($files as $file) {
            if (preg_match($query, basename($file))) {
                $matched_files[] = $file;
            }
        }

        return "Matched files:\n".implode("\n", $matched_files);
    }
}
