<?php

namespace App\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class InstallCommand extends Command
{
    // @ignore
    protected $signature = 'install';

    protected $description = 'Install the command line tool';

    protected string $appDir;

    public function handle()
    {
        $this->appDir = app_storage_path();
        if (env('APP_ENV') === 'development') {
        $this->error('⚠️  You are running the development version of the command line tool.');
        }
        $this->line('📁 App directory: '.$this->appDir);
        if (! $this->init()) {
            return;
        }
        $this->createDatabase();
        $this->createEnvFile();
        $this->migrateDatabase();
    }

    public function init(): bool
    {
        $paths = [
            $this->appDir,
            $this->appDir.DIRECTORY_SEPARATOR.'database',
            $this->appDir.DIRECTORY_SEPARATOR.'cache',
        ];
        foreach ($paths as $path) {
        if (! File::exists($path)) {
        File::makeDirectory($path, 0755);
        }
        }

        $dbFile = $paths[1].DIRECTORY_SEPARATOR.'database.sqlite';
        $envFile = $this->appDir.DIRECTORY_SEPARATOR.'.env';

        if (File::exists($dbFile) || File::exists($envFile)) {
            $choice = $this->choice('Some files already exist. Do you want to delete the files and start fresh or cancel the installation?', ['Delete and start fresh', 'Cancel'], 1);
            if ($choice === 'Delete and start fresh') {
                File::delete([$dbFile, $envFile]);
                File::deleteDirectory($this->appDir);
            } else {
                $this->line(PHP_EOL.'Installation canceled.');

                return false;
            }
        }

        return true;
    }

    protected function createDatabase()
    {
        $this->task('Creating database.sqlite file', function () {
            $databaseFile = $this->appDir.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.sqlite';
            $this->line(PHP_EOL."📄 Database file: {$databaseFile}");
            File::put($databaseFile, '');
            File::chmod($databaseFile, 0755);

            return true;
        });
    }

    protected function createEnvFile()
    {
        $this->task('Creating .env file', function () {
            $envFile = $this->appDir.DIRECTORY_SEPARATOR.'.env';
            $this->line(PHP_EOL."📄 .env file: {$envFile}");
            if (! File::exists($envFile)) {
                $openAiApiKey = $this->ask('Please provide your OpenAI API key:');
                $envContent = "CACHE_FOLDER=$this->appDir/cache\nOPENAI_API_KEY={$openAiApiKey}";
                File::put($envFile, $envContent);
                File::chmod($envFile, 0755);

                return true;
            }
            $this->line('ℹ️  .env file already exists.');

            return false;
        });
    }

    protected function migrateDatabase()
    {
        $this->task('Migrating the database', function () {
            $dotenv = \Dotenv\Dotenv::createUnsafeImmutable($this->appDir.DIRECTORY_SEPARATOR, '.env');
            $dotenv->load();
            $this->line(PHP_EOL.'📄 Current database file configuration: '.config('database.connections.sqlite.database').PHP_EOL);
            Artisan::call('migrate', ['--force' => true]);

            return true;
        });
    }
}
