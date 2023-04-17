<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class RunActionCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run:action {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Test an action by name as defined in config/actions.php';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $actionName = $this->argument('name');
        $actionConfig = config("actions.actions.{$actionName}");

        if (! $actionConfig) {
            $this->error("Action {$actionName} not found.");

            return;
        }

        $actionClass = $actionConfig['class'];
        $actionArguments = $actionConfig['arguments'];

        $arguments = [];

        foreach ($actionArguments as $argumentName => $argumentConfig) {
            $arguments[$argumentName] = $this->ask($argumentConfig['description']);
        }

        $action = new $actionClass($this);

        $result = $action->execute($arguments);

        $this->info($result);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
