<?php

namespace PauloCoelho\ScaffoldPlus;

use Illuminate\Support\ServiceProvider;
use PauloCoelho\ScaffoldPlus\Console\MakeEstruturaCommand;

class ScaffoldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            MakeEstruturaCommand::class,
        ]);
    }

    public function boot(): void
    {
        //
    }
}