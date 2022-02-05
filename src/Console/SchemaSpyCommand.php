<?php

/**
 * This file is part of LaravelSchemaSpicy
 *
 */

namespace Suzunone\LaravelSchemaSpicy\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class SchemaSpyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema-spicy:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create schemaspy command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Repository $config)
    {
        $Schema_Spicy_config = $config->get('schema-spicy');

        $cmd = 'java -jar "' . $Schema_Spicy_config['schemaspy_jar_path'] . '" "' . $Schema_Spicy_config['schemaspy_properties_path'] . '" -vizjs';

        system($cmd);
    }
}
