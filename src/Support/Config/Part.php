<?php

namespace MdTech\Modules\Support\Config;

class Part
{
    private $name;
    private $generate;
    private $onlyService;
    private $namespace;

    public function __construct($config)
    {
        if (is_array($config)) {
            $this->name = $config['name'];
            $this->generate = $config['generate'];
            $this->onlyService = $config['onlyService'];
            $this->namespace = $config['namespace'] ?? $this->convertPathToNamespace($config['name']);

            return;
        }
        $this->name = $config;
        $this->generate = (bool) $config;
        $this->namespace = $config;
    }

    public function getName()
    {
        return $this->name;
    }

    public function generate() : bool
    {
        return $this->generate;
    }

    public function onlyService() : bool
    {
        return $this->onlyService;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    private function convertPathToNamespace($name)
    {
        return str_replace('/', '\\', $name);
    }
}
