#!/bin/bash

CONFIG_FILE="config/actions.php"
ACTIONS_DIR="app/Actions"

# Read the config file to get the class names
CLASS_NAMES=$(grep -oP '(?<=class => ).*::class' $CONFIG_FILE)

for class_name in $CLASS_NAMES; do
    # Remove the namespace and class prefix
    class_file=$(echo $class_name | sed 's/App\\Actions\\//;s/::class//')

    # Create the file with the class template
    cat > $ACTIONS_DIR/$class_file.php << EOF
<?php

namespace App\Actions;

class $class_file extends Action
{
    public function execute(array \$args): string
    {
        // TODO: Implement the execute() method
    }
}
EOF
done
