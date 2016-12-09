<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ninja:make-module {name} {fields?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Module CRUD';

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
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $fields = $this->argument('fields');
        $lower = strtolower($name);

        // convert 'name:string,description:text' to 'name,description'
        $fillable = explode(',', $fields);
        $fillable = array_map(function($item) {
            return explode(':', $item)[0];
        }, $fillable);
        $fillable = join(',', $fillable);

        $this->info("Creating module: {$name}");

        Artisan::call('module:make', ['name' => [$name]]);
        Artisan::call('module:make-migration', ['name' => "create_{$lower}_table", '--fields' => $fields, 'module' => $name]);
        Artisan::call('module:migrate', ['module' => $name]);
        Artisan::call('module:make-model', ['model' => $name, 'module' => $name, '--fillable' => $fillable]);

        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'views', '--fields' => $fields, '--filename' => 'edit.blade']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'datatable', '--fields' => $fields]);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'repository', '--fields' => $fields]);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'policy']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'auth-provider']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'presenter']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'request']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'request', 'prefix' => 'create']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'request', 'prefix' => 'update']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'api-controller']);
        Artisan::call('ninja:make-class', ['name' => $name, 'module' => $name, 'class' => 'transformer', '--fields' => $fields]);

        Artisan::call('module:dump');
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module.'],
            ['fields', InputArgument::OPTIONAL, 'The fields of the module.']
        ];
    }

}