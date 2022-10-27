<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * Создать ресурс для модели.
 */
class MakeResource extends GeneratorCommand
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $name = 'model:resource {name}';

    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Создать класс-ресурс для модели';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model resource';

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $model = str_replace('Resource', '', $this->getNameInput());
        if (!class_exists("\App\Models\\".$model)) {
            return $this->error('Модель ' . $model . ' не существует!');
        }
        return parent::handle();
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return parent::qualifyClass($name);
    }

    /**
    * Get the stub file for the generator.
    *
    * @return string
    */
    protected function getStub()
    {
        return app_path().'/Console/Commands/Stubs/make-resource.stub';
    }

    /**
    * Get the default namespace for the class.
    *
    * @param  string  $rootNamespace
    * @return string
    */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Resources';
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
        $class = $this->getNameInput();
        $model = str_replace('Resource', '', $class);
  
        $stub = str_replace(['{{ Model }}', '{{Model}}'], ucfirst($model), $stub);
        $stub = str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
        $stub = str_replace(['{{ model }}', '{{model}}'], lcfirst($model), $stub);
        return $stub;
    }
}