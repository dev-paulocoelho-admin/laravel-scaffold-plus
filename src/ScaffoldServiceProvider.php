<?php

namespace PauloCoelho\LaravelScaffoldPlus;

use Illuminate\Support\ServiceProvider;
use PauloCoelho\LaravelScaffoldPlus\Console\EstruturaCommand;

class ScaffoldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            \PauloCoelho\LaravelScaffoldPlus\Console\EstruturaCommand::class,
        ]);
    }

    public function boot(): void
    {
        //
    }
}
