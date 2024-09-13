<?php

namespace Hasanweb\Blueprint\Commands;

use Hasanweb\Blueprint\Controllers\Controller;
use Hasanweb\Blueprint\Migrations\Migration;
use Hasanweb\Blueprint\Models\Model;
use Hasanweb\Blueprint\Repositories\Repository;
use Hasanweb\Blueprint\Routes\Route;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Helper\Table;

class Blueprint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:make {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scaffold you app with ease';

    public function generateRepositorySyntaxArray($models)
    {
        $repositories = [];

        foreach ($models as $key => $value) {
            $repositoryName = $key.'Repository';
            $repositories[$repositoryName] = ['model' => $key];
        }

        return $repositories;
    }

    public function generateControllerSyntaxArray($models)
    {
        $controllers = [];

        foreach ($models as $key => $value) {
            $controllerName = $key.'Controller';
            $controllers[$controllerName] = [
                'repository' => $key.'Repository',
            ];
        }

        return $controllers;
    }

    public function generateRouteResourcesSyntaxArray($models)
    {

        $resources = [];

        foreach ($models as $key) {
            $routeName = strtolower($key);
            $resources['resources'][$routeName] = $key.'Controller';
        }

        return $resources;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create a new Table instance.
        $table = new Table($this->output);

        // Get the filename argument
        $filename = $this->argument('filename');

        // Define the file path
        $filePath = base_path($filename);

        // Check if the file exists
        if (! File::exists($filePath)) {
            $this->error("File not found: $filename");

            return;
        }

        // Read the file content
        $jsonContent = File::get($filePath);

        // Decode the JSON content
        $data = json_decode($jsonContent, true);

        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to decode JSON: '.json_last_error_msg());

            return;
        }

        // generate migartions files and migrate them
        Migration::make($data['migrations'], $table, $data['with-seeders']);

        $models = $data['models'];

        // generate model files
        Model::make($models, $table);

        if ($data['with-controller-resources']) {
            // generate repository files
            $repositories = $this->generateRepositorySyntaxArray($models);
            Repository::make($repositories, $table);

            // generate controller files
            $controllers = $this->generateControllerSyntaxArray($models);
            Controller::make($controllers, $table);

            // generate route files
            $routeResources = $this->generateRouteResourcesSyntaxArray(array_keys($models));
            Route::make($routeResources);
            $this->info('routes generated successfully');
        }

        // generate filament resources if the user wants it
        if ($data['with-filament-resources']) {
            foreach ($data['models'] as $modelName => $fields) {
                Artisan::call('make:filament-resource '.$modelName.' --generate --force');
            }
        }

        $this->info('Blueprint generated successfully');
    }
}
