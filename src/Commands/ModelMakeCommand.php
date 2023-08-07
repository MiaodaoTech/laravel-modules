<?php

namespace MdTech\Modules\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use MdTech\Modules\Support\Config\GenerateConfigReader;
use MdTech\Modules\Support\Config\PartConfigReader;
use MdTech\Modules\Support\Stub;
use MdTech\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'model';

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
    protected $name = 'module:make-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new model for the specified module.';

    /**
     * Get model name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $modelPath = GenerateConfigReader::read('model');

        $path = $this->laravel['modules']->getFilePath($modelPath->getPath(), $this->getModuleName());

        return $path . '/' . $this->getModelName() . '.php';
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        return (new Stub($this->getStubName(), [
            'MODULENAME'        => Str::studly($this->getModuleName()),
            'CONTROLLERNAME'    => $this->getModelName(),
            'CLASS_NAMESPACE'   => $this->getModelNamespace(),
            'CLASS'             => $this->getModelNameWithoutNamespace(),
            'MODULE'            => $this->getModuleName(),
            'NAME'              => $this->getModelName(),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
            'FILLABLE'          => $this->getFillable(),
            'CASTS'             => $this->getCasts(),
            'TABLE_NAME'        => $this->getTableName(),
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
            ['model', InputArgument::REQUIRED, 'The name of the model class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['plain', 'p', InputOption::VALUE_NONE, 'Generate a plain model', null],
            ['force', null, InputOption::VALUE_NONE, 'Overwrite exist files.'],
        ];
    }

    /**
     * @return array|string
     */
    private function getModelNameWithoutNamespace()
    {
        return class_basename($this->getModelName());
    }

    /**
     * @return string
     */
    private function getExtendsModel(){
        if($this->argument('part') == 'common'){
            return "\App\Http\Models\BaseModel";
        }else{
            return '\\' . $this->getModelNamespace() . '\\' . $this->getModuleName() . PartConfigReader::read('common')->getName() . 'Model';
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
        $model = Str::studly($this->argument('model'));

        if (Str::contains(strtolower($model), 'model') === false) {
            $model .= 'Model';
        }

        return $model;
    }

    /**
     * @name getTableName
     * @description 根据模块名称获取表名
     * @return string
     * @author h2odumpling
     * @date 2023/7/13
     */
    protected function getTableName(){
        $count = 0;
        $name = preg_replace_callback('/[A-Z]/', function ($match) use (&$count){
            $count++;
            $str = strtolower($match[0]);
            if($count > 1){
                $str = "_" . $str;
            }
            return $str;
        }, Str::studly($this->argument('model')));
        return $name;
    }

    /**
     * @name tableExists
     * @description 返回是否存在表
     * @param $table
     * @return boolean
     * @author h2odumpling
     * @date 2023/7/13
     */
    private function tableExists($table){
        return Schema::hasTable($table);
    }

    /**
     * @name getFillable
     * @description 获取laravel model fillable
     * @return array|string|string[]|null
     * @author h2odumpling
     * @date 2023/7/13
     */
    protected function getFillable()
    {
        //这里不使用Schema::getColumnListing('table')的原因是可能会乱序
        $table = $this->getTableName();

        if(!$this->tableExists($table)) return "[]";

        $table_info_columns = DB::select(DB::raw('SHOW COLUMNS FROM '. env('DB_PREFIX') . $table));
        $string = "[";
        foreach ($table_info_columns as $v){
            $string .= '"' . $v->Field . '",';
        }
        $string.="]";
        return $this->venderStubArray($string);
    }

    /**
     * @name getCasts
     * @description 获取laravel model casts
     * @return array|string|string[]|null
     * @author h2odumpling
     * @date 2023/7/13
     */
    protected function getCasts()
    {
        $table = $this->getTableName();

        if(!$this->tableExists($table)) return "[]";

        $table_info_columns = DB::select(DB::raw('SHOW COLUMNS FROM '. env('DB_PREFIX') . $table));
        $string = "[";
        foreach ($table_info_columns as $v){
            $string .= '"' . $v->Field . '" => "' . $this->changeColumnType($v->Type) . '",';
        }
        $string.="]";
        return $this->venderStubArray($string);
    }

    /**
     * @name venderStubArray
     * @description 将一维数组格式化
     * @param $string
     * @return array|string|string[]|null
     * @author h2odumpling
     * @date 2023/7/13
     */
    protected function venderStubArray($string){
        $string = preg_replace("/([\[|,])/","$1\n        ", $string);
        $string = preg_replace("/    (\])/","$1", $string);
        return $string;
    }

    /**
     * @name changeColumnType
     * @description 根据mysql type转换为laravel type
     * @param $type
     * @return string
     * @author h2odumpling
     * @date 2023/7/13
     */
    protected function changeColumnType($type){
        switch ($type){
            case 'date':
                $type = 'date';
                break;
            case 'datetime':
            case 'timestamp':
                $type = 'datetime';
                break;
            case 'varchar':
            case 'char':
            case 'text':
                $type = 'string';
                break;
            case 'json':
                $type = 'array';
                break;
            default:
                preg_match("/(varchar)|(char)|(text)/", $type, $match);
                if(!empty($match)){
                    $type = 'string';
                }else{
                    $type = 'numeric';
                }
        }
        return $type;
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.model.namespace') ?: $module->config('paths.generator.model.path', 'Http/Models');
    }

    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        if ($this->option('plain') === true) {
            $stub = '/model-plain.stub';
        } else {
            $stub = '/model.stub';
        }

        return $stub;
    }
}
