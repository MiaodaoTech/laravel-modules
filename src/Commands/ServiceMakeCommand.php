<?php

namespace MdTech\Modules\Commands;

use Illuminate\Support\Str;
use MdTech\Modules\Support\Config\GenerateConfigReader;
use MdTech\Modules\Support\Config\PartConfigReader;
use MdTech\Modules\Support\Stub;
use MdTech\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ServiceMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'service';

    /**
     * Mid-part of the name.
     *
     * @var string
     */
    protected $part = '';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new service for the specified module.';

    /**
     * Get service name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $servicePath = GenerateConfigReader::read('service');

        $path = $this->laravel['modules']->getFilePath($servicePath->getPath(), $this->getMidPart());

        return $path . '/' . $this->getServiceName() . '.php';
    }

    /**
     * Get midpart of the name.
     *
     * @return string
     */
    public function getMidPart(){
        $part = $this->getPart()->getName();

        return GenerateConfigReader::read('service')->generate() ? $this->getModuleName() : $part;
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        return (new Stub($this->getStubName(), [
            'MODULENAME'        => Str::studly($this->getModuleName()),
            'CONTROLLERNAME'    => $this->getServiceName(),
            'CLASS_NAMESPACE'   => $this->getServiceNamespace(),
            'CLASS'             => $this->getServiceName(),
            'MODULE'            => $this->getModuleName(),
            'NAME'              => $this->getModuleName(),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
            'MODEL_NAME'        => $this->getModelName(),
            'MODEL_NAMESPACE'   => $this->getModelNamespace(),
            'EXTENDS_SERVICE'   => $this->getExtendsService(),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['service', InputArgument::REQUIRED, 'The name of the service class.'],
            ['part', InputArgument::REQUIRED, 'The mid-part of the service class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['plain', 'p', InputOption::VALUE_NONE, 'Generate a plain service', null],
            ['force', null, InputOption::VALUE_NONE, 'Overwrite exist files.'],
        ];
    }

    /**
     * @return string
     */
    protected function getServiceName()
    {
        $service = Str::studly($this->argument('service'));

        if(GenerateConfigReader::read('service')->generate() === true){
            $service .= $this->getPart()->getName();
        }

        if (Str::contains(strtolower($service), 'service') === false) {
            $service .= 'Service';
        }

        return $service;
    }

    /**
     * @return string
     */
    protected function getServiceNamespace()
    {
        return $this->laravel['modules']->config('namespace') . '\\' .GenerateConfigReader::read('service')->getNamespace() . '\\' . $this->getModuleName();
    }

    /**
     * @return array|string
     */
    private function getServiceNameWithoutNamespace()
    {
        return class_basename($this->getServiceName());
    }

    /**
     * @return string
     */
    private function getExtendsService(){
        if($this->argument('part') == 'common'){
            return "\App\Http\Services\BaseService";
        }else{
            return '\\' . $this->getServiceNamespace() . '\\' . $this->getModuleName() . PartConfigReader::read('common')->getName() . 'Service';
        }
    }

    /**
     * @return string
     */
    protected function getModelNamespace()
    {
        return $this->laravel['modules']->config('namespace') . '\\' .GenerateConfigReader::read('model')->getNamespace() . '\\' . $this->getModuleName();
    }

    /**
     * @return string
     */
    protected function getModelName()
    {
        return $this->argument('service') . 'Model';
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.service.namespace') ?: $module->config('paths.generator.service.path', 'Http/Services');
    }

    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        if ($this->option('plain') === true) {
            $stub = '/service-plain.stub';
        } else {
            $stub = '/service.stub';
        }

        return $stub;
    }
}
