<?php

/**
 * This file is part of LaravelSchemaSpicy
 *
 */

namespace Suzunone\LaravelSchemaSpicy\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class SchemaSpyPropertyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema-spicy:property';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create schema-spicy property';

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
        $database_config = $config->get('database');
        $schema_spicy_config = $config->get('schema-spicy');

        $template_path = dirname(__DIR__) . '/stub/schemaspy.properties';

        $template = file_get_contents($template_path);

        $type = $database_config['default'];

        $properties = str_replace(
            [
                '__META_PATH__',
                '__JDBC__',
                '__TYPE__',
                '__HOST__',
                '__PORT__',
                '__DBNAME__',
                '__USER__',
                '__PASSWORD__',
                '__SAVE_PATH__',
            ],
            [
                $schema_spicy_config['schema_meta_path'],
                $schema_spicy_config['jdbc_driver_path'],
                $database_config['connections'][$type]['driver'],
                $database_config['connections'][$type]['host'],
                $database_config['connections'][$type]['port'],
                $database_config['connections'][$type]['database'],
                $database_config['connections'][$type]['username'],
                $database_config['connections'][$type]['password'],
                $schema_spicy_config['er_save_path'],
            ],
            $template
        );

        file_put_contents($schema_spicy_config['schemaspy_properties_path'], $properties);

        $this->info('Put property on' . $schema_spicy_config['schemaspy_properties_path']);
    }
}
