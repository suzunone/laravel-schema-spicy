<?php

/**
 * This file is part of LaravelSchemaSpicy
 *
 */

namespace Suzunone\LaravelSchemaSpicy\Console;

use Composer\Autoload\ClassMapGenerator;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use SimpleXMLElement;
use Symfony\Component\Console\Output\OutputInterface;

class SchemaSpyXMLCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema-spicy:xml {--check-relate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create schema-spicy meta xml';

    private $xml_template;

    /**
     * Execute the console command.
     *
     * @throws \JsonException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return int
     */
    public function handle(Repository $config)
    {
        $model_namespace = $config->get('schema-spicy')['model_name_space'];
        $exclude_classes = $config->get('schema-spicy')['exclude_classes'];
        $xml_file = $config->get('schema-spicy')['schema_meta_path'];
        $this->xml_template = file_get_contents(dirname(__DIR__) . '/stub/schemameta.xml');

        $template_path = $config->get('schema-spicy')['schema_meta_template_path'];
        if ($template_path !== null && is_file($template_path)) {
            $this->xml_template = file_get_contents($template_path);
        }

        $composer_json = json_decode(file_get_contents(base_path('composer.json')), true, 512, JSON_THROW_ON_ERROR);
        $autoload_psr4 = $composer_json['autoload']['psr-4'];

        $model_path = null;
        foreach ($autoload_psr4 as $ns => $path) {
            if (Str::startsWith($model_namespace, $ns)) {
                $model_path = $path . str_replace('\\', '/', Str::after($model_namespace, $ns));

                break;
            }
        }

        if (is_null($model_path)) {
            $this->error('Specified namespace is incorrect.');

            return -1;
        }
        $this->info('Models path: ' . $model_path);

        if (!is_dir($model_path)) {
            $this->error('Not found the Models path.');

            return -1;
        }

        $target_models = $this->loadModels([$model_path]);

        if (empty($target_models)) {
            $this->line('Nothing to generate xml. The models are not found.');

            return -1;
        }

        $target_models = array_filter($target_models, static function ($item) use ($exclude_classes) {
            return !in_array($item, $exclude_classes, true);
        });

        $result = $this->generate($target_models, $xml_file);

        if ($result === false) {
            $this->error('Failed to write to xml.');
        }

        return 0;
    }

    /**
     * @param mixed $target_models
     * @param mixed $xml_path
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function generate($target_models, $xml_path)
    {
        $sxe = new SimpleXMLElement($this->xml_template);
        foreach ($target_models as $target_model) {
            if (!class_exists($target_model)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($target_model);
            if (!$reflectionClass->isSubclassOf(Model::class)) {
                continue;
            }

            if (!$reflectionClass->IsInstantiable()) {
                // ignore abstract class or interface
                continue;
            }

            $model = $this->laravel->make($target_model);

            $this->comment("Loading model '${target_model}'", OutputInterface::VERBOSITY_VERBOSE);

            foreach ($reflectionClass->getMethods() as $method) {
                $this->addRelationshipNodeToXml($sxe->tables, $model, $reflectionClass, $method);
            }
        }

        $sxe->saveXML($xml_path);

        return true;
    }

    public function getRelateByDoc(ReflectionMethod $reflectionMethod)
    {
        $docComment = $reflectionMethod->getDocComment();

        if (!mb_eregi('@return +([^ ]*)', $docComment, $annotation)) {
            return null;
        }

        $annotation[1] = trim($annotation[1]);
        $annotation[1] = ltrim($annotation[1], '\\');
        switch ($annotation[1]) {
            case BelongsTo::class:
                return BelongsTo::class;
            case HasOne::class:
                return HasOne::class;
            case HasMany::class:
                return HasMany::class;
        }

        switch (true) {
            case strtolower($annotation[1]) === 'belongsto':
                return BelongsTo::class;
            case strtolower($annotation[1]) === 'hasone':
                return HasOne::class;
            case strtolower($annotation[1]) === 'hasmany':
                return HasMany::class;
        }

        return $annotation[1];
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     * @return string
     */
    public function getRelate(ReflectionMethod $reflectionMethod):string
    {
        $relate = optional($reflectionMethod->getReturnType())->getName();

        switch (true) {
            case $relate === BelongsTo::class:
            case $relate === HasOne::class:
            case $relate === HasMany::class:
                return $relate;
        }

        return $this->getRelateByDoc($reflectionMethod) ?? $relate;
    }

    /**
     * @param $base_dirs
     * @return array
     */
    protected function loadModels($base_dirs): array
    {
        $models = [];
        foreach ($base_dirs as $base_dir) {
            if (is_dir(base_path($base_dir))) {
                $base_dir = base_path($base_dir);
            }

            $dirs = glob($base_dir, GLOB_ONLYDIR);

            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    $this->error("Cannot locate directory '{$dir}'");

                    continue;
                }

                if (file_exists($dir)) {
                    $classMap = ClassMapGenerator::createMap($dir);

                    // Sort list so it's stable across different environments
                    ksort($classMap);

                    foreach ($classMap as $model => $path) {
                        $models[] = $model;
                    }
                }
            }
        }

        return $models;
    }

    /**
     * @param \SimpleXMLElement $sxe
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \ReflectionClass $reflectionClass
     * @param \ReflectionMethod $reflectionMethod
     * @return void
     */
    protected function addRelationshipNodeToXml(SimpleXMLElement $sxe, Model $model, ReflectionClass $reflectionClass, ReflectionMethod $reflectionMethod): void
    {
        if ($reflectionMethod->getParameters()) {
            return;
        }

        if (!$reflectionMethod->isPublic()) {
            return;
        }

        $method = $reflectionMethod->getName();
        if (
            method_exists(Model::class, $method)
            || Str::startsWith($method, 'get')
        ) {
            return;
        }

        $relate = $this->getRelate($reflectionMethod);

        $class_name = $reflectionClass->getName();

        switch ($relate) {
            case BelongsTo::class:
            case HasOne::class:
            case HasMany::class:
            $relationship = $model->{$method}();

            break;
            default:
                return;
        }

        switch (get_class($relationship)) {
            case BelongsTo::class:
                $this->info($class_name . '::' . $method);

                if ($this->option('check-relate')) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $data = $class_name::first();

                    if (!($data && $data->{$method})) {
                        $this->error($class_name . '::' . $method . ' is no data');
                    }
                }

                $related_table = $model->getTable();
                $parent_table = $relationship->getRelated()->getTable();

                $foreign_key = $relationship->getForeignKeyName();
                $local_key = $relationship->getOwnerKeyName();

                break;
            case HasOne::class:
            case HasMany::class:
                $this->info($class_name . '::' . $method);
                if ($this->option('check-relate')) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $data = $class_name::first();

                    if (!($data && $data->{$method})) {
                        $this->error($class_name . '::' . $method . ' is no data');
                    }
                }

                $parent_table = $model->getTable();
                $related_table = $relationship->getRelated()->getTable();

                $foreign_key = $relationship->getForeignKeyName();

                $local_key = $relationship->getLocalKeyName();

                break;
            default:
                return;
        }

        $getTable = static function ($sxe) use ($related_table) {
            return $sxe->xpath("table[@name=\"{$related_table}\"]")[0] ?? null;
        };

        $getColumn = static function ($sxe) use ($foreign_key) {
            return $sxe->xpath("column[@name=\"{$foreign_key}\"]")[0] ?? null;
        };

        $getForeignKey = static function ($sxe) use ($parent_table, $local_key) {
            return $sxe->xpath("foreignKey[@table=\"{$parent_table}\"][@column=\"{$local_key}\"]")[0] ?? null;
        };

        if (is_null($getTable($sxe))) {
            optional($sxe->addChild('table'))
                ->addAttribute('name', $related_table);
        }

        if (is_null($getColumn($getTable($sxe)))) {
            optional(optional($getTable($sxe))->addChild('column'))
                ->addAttribute('name', $foreign_key);
        }

        if (is_null($getForeignKey($getColumn($getTable($sxe))))) {
            $node = optional($getColumn($getTable($sxe)))->addChild('foreignKey');
            $node->addAttribute('table', $parent_table);
            $node->addAttribute('column', $local_key);
        }
    }
}
