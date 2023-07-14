<?php

namespace MdTech\Modules\Traits;

use MdTech\Modules\Support\Config\Part;
use MdTech\Modules\Support\Config\PartConfigReader;

trait ModuleCommandTrait
{
    /**
     * Get the module name.
     *
     * @return string
     */
    public function getModuleName():string
    {
        $module = $this->argument('module') ?: app('modules')->getUsedNow();
        return $module;
    }

    /**
     * Get part reader.
     *
     * @return Part
     */
    public function getPart():Part
    {
        return PartConfigReader::read($this->argument('part'));
    }
}
