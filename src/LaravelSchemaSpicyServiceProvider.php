<?php

/**
 * This file is part of LaravelSchemaSpicy
 *
 */

namespace Suzunone\LaravelSchemaSpicy;

use Illuminate\Support\ServiceProvider;
use Suzunone\LaravelSchemaSpicy\Console\SchemaSpyCommand;
use Suzunone\LaravelSchemaSpicy\Console\SchemaSpyPropertyCommand;
use Suzunone\LaravelSchemaSpicy\Console\SchemaSpyXMLCommand;

class LaravelSchemaSpicyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/schema-spicy.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('schema-spicy.php');
        } else {
            $publishPath = base_path('config/schema-spicy.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');
        //Register commands
        $this->commands([
            SchemaSpyXMLCommand::class,
            SchemaSpyPropertyCommand::class,
            SchemaSpyCommand::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/schema-spicy.php';
        $this->mergeConfigFrom($configPath, 'schema-spicy');
    }
}
