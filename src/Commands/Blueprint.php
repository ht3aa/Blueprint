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

    /**
     * Execute the console command.
     */
    public function handle()
    {
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
        Migration::make($data['migrations']);
        Artisan::call('migrate:fresh');
        $this->info('Migration generated successfully');

        // generate model files
        Model::make($data['models']);
        $this->info('Model generated successfully');

        // generate repository files
        Repository::make($data['repositories']);
        $this->info('repositories generated successfully');

        // generate controller files
        Controller::make($data['controllers']);
        $this->info('controllers generated successfully');

        // generate route files
        Route::make($data['routes']);
        $this->info('routes generated successfully');

        // generate filament resources if the user wants it
        if ($data['with-filament-resources']) {
            foreach ($data['models'] as $modelName => $fields) {
                Artisan::call('make:filament-resource '.$modelName.' --generate --force');
            }
        }
        $this->info('Blueprint generated successfully');
    }
}
