<?php

namespace App\Commands;

use App\Agents\MotherAgent;
use App\ChatGPT;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class StartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start {mode=confirmation : Mode can be "confirmation" or "continuous"}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Starts a session with regelneef bot';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ChatGPT $chat)
    {
        $mode = $this->argument('mode');

        if (! in_array($mode, ['confirmation', 'continuous'])) {
            $this->error('❌ Invalid mode. Choose "confirmation" or "continuous"');

            return;
        }

        $this->info("⚙️ Starting in $mode mode");

        $agent = (new MotherAgent($chat, $this, $mode === 'continuous'))
            ->activate();

        if ($agent->isSuccess()) {
            return $this->info('🎉 Success!');
        }

        return $this->error('❌ Failed!');
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
