<?php

namespace MdTech\Modules\Commands;

use Illuminate\Support\Str;
use MdTech\Modules\Support\Config\GenerateConfigReader;
use MdTech\Modules\Support\Config\PartConfigReader;
use MdTech\Modules\Support\Stub;
use MdTech\Modules\Traits\ModuleCommandTrait;
use phpDocumentor\Reflection\Utils;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'controller';

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
    protected $name = 'module:make-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new restful controller for the specified module.';

    /**
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $controllerPath = GenerateConfigReader::read('controller');

        $path = $this->laravel['modules']->getFilePath($controllerPath->getPath(), $this->getMidPart());

        return $path . '/' . $this->getControllerName() . '.php';
    }

    /**
     * Get midpart of the name.
     *
     * @return string
     */
    public function getMidPart(){
        $part = $this->getPart()->getName();

        return GenerateConfigReader::read('controller')->generate() ? $this->getModuleName() : $part;
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        return (new Stub($this->getStubName(), [
            'MODULENAME'        => Str::studly($this->getModuleName()),
            'CONTROLLERNAME'    => $this->getControllerName(),
            'CLASS_NAMESPACE'   => $this->getControllerNamespace(),
            'CLASS'             => $this->getControllerName(),
            'MODULE'            => $this->getModuleName(),
            'NAME'              => $this->getModuleName(),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
            'SERVICE_NAME'      => $this->getServiceName(),
            'SERVICE_NAMESPACE' => $this->getServiceNamespace(),
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
            ['controller', InputArgument::REQUIRED, 'The name of the controller class.'],
            ['part', InputArgument::REQUIRED, 'The mid-part of the controller class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['plain', 'p', InputOption::VALUE_NONE, 'Generate a plain controller', null],
            ['force', null, InputOption::VALUE_NONE, 'Overwrite exist files.'],
        ];
    }

    /**
     * @return array|string
     */
    protected function getControllerName()
    {
        $controller = Str::studly($this->argument('controller'));

        if(GenerateConfigReader::read('controller')->generate() === true){
            $controller .= $this->getPart()->getName();
        }

        if (Str::contains(strtolower($controller), 'controller') === false) {
            $controller .= 'Controller';
        }

        return $controller;
    }

    /**
     * @return string
     */
    protected function getControllerNamespace(){
        return $this->laravel['modules']->config('namespace') . '\\' .GenerateConfigReader::read('controller')->getNamespace() . '\\' . $this->getMidPart();
    }

    /**
     * @return string
     */
    protected function getServiceName(){
        return $this->argument('controller') . $this->getPart()->getName() . 'Service';
    }

    /**
     * @return string
     */
    protected function getServiceNamespace(){
        return $this->laravel['modules']->config('namespace') . '\\' .GenerateConfigReader::read('service')->getNamespace() . '\\' . $this->getModuleName();
    }

    /**
     * @return array|string
     */
    private function getControllerNameWithoutNamespace()
    {
        return class_basename($this->getControllerName());
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.controller.namespace') ?: $module->config('paths.generator.controller.path', 'Http/Controllers');
    }

    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        if ($this->option('plain') === true) {
            $stub = '/controller-plain.stub';
        } else {
            $stub = '/controller.stub';
        }

        return $stub;
    }
}
