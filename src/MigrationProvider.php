<?php

namespace Wikko\Googlesheet;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class MigrationProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}