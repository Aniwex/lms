<?php

namespace App\Console\Commands;

use App\Models\Integration;
use Illuminate\Console\GeneratorCommand;
use PhpParser\Node\Scalar\MagicConst\Dir;

/**
 * Добавить новую интеграцию в систему.
 */
class CreateIntegration extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integration:new {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавить новую интеграцию в систему';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Класс интеграции';

    /**
     * @var string
     */
    protected string $integrationClassName = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slug = strtolower(trim($this->argument('name') ?? ''));
        $name = ucfirst($slug);
        $this->integrationClassName = $name;

        $class = '\App\Integrations\\' . $name . '\\' . $name;

        $modelExists = Integration::whereSlug($slug)->exists();

        if (class_exists($class) && $modelExists) {
            return $this->error('Такая интеграция уже существует в системе');
        }

        $handle = true;

        if (!class_exists($class)) {
            $dir = app()->basePath('app' . DIRECTORY_SEPARATOR . 'Integrations' . DIRECTORY_SEPARATOR . $name);
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $handle = parent::handle();
        }

        if (!$modelExists) {
            Integration::create([
                'title' => $this->ask('Укажите название интеграции (на русском языке)'),
                'slug' => $slug
            ]);
            $this->info('Модель интеграции добавлена в базу данных');
        }

        return $handle;
    }

    /**
    * Get the stub file for the generator.
    *
    * @return string
    */
    protected function getStub()
    {
        return app_path().'/Console/Commands/Stubs/create-integration.stub';
    }

    /**
    * Get the default namespace for the class.
    *
    * @param  string  $rootNamespace
    * @return string
    */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Integrations\\'.$this->integrationClassName;
    }


    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        return str_replace(['{{ Integration }}', '{{Integration}}'], ucfirst($this->integrationClassName), $stub);
    }
}
