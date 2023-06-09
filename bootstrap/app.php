<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new LaravelZero\Framework\Application(
    dirname(__DIR__)
);

$pharRunning = Phar::running(false);

if (! empty($pharRunning)) {
    if (file_exists($envPath)) {
        // Load the .env file from the home directory.
        $app->useEnvironmentPath(app_storage_path().DIRECTORY_SEPARATOR.'.env');
    } else {
        // Use the default .env selection logic.
        $app->useEnvironmentPath($app->basePath());
    }
} else {
    // Use the default .env selection logic.
    $app->useEnvironmentPath($app->basePath());
}

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    LaravelZero\Framework\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Illuminate\Foundation\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
