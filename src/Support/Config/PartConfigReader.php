<?php

namespace MdTech\Modules\Support\Config;

class PartConfigReader
{
    public static function read(string $value) : Part
    {
        return new Part(config("modules.paths.part.$value"));
    }
}
