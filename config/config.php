<?php

use MdTech\Modules\Activators\FileActivator;
use MdTech\Modules\Commands;

return [

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | Default module namespace.
    |
    */

    'namespace' => 'App',

    /*
    |--------------------------------------------------------------------------
    | Module Stubs
    |--------------------------------------------------------------------------
    |
    | Default module stubs.
    |
    */

    'stubs' => [
        'enabled' => false,
        'path' => base_path('vendor/nwidart/laravel-modules/src/Commands/stubs'),
        'files' => [
//            'routes/web' => 'Routes/web.php',
//            'routes/api' => 'Routes/api.php',
//            'views/index' => 'Resources/views/index.blade.php',
//            'views/master' => 'Resources/views/layouts/master.blade.php',
//            'scaffold/config' => 'Config/config.php',
        ],
        'replacements' => [
//            'routes/web' => ['LOWER_NAME', 'STUDLY_NAME'],
//            'routes/api' => ['LOWER_NAME'],
//            'webpack' => ['LOWER_NAME'],
//            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
//            'views/index' => ['LOWER_NAME'],
//            'views/master' => ['LOWER_NAME', 'STUDLY_NAME'],
//            'scaffold/config' => ['STUDLY_NAME'],
//            'composer' => [
//                'LOWER_NAME',
//                'STUDLY_NAME',
//                'VENDOR',
//                'AUTHOR_NAME',
//                'AUTHOR_EMAIL',
//                'MODULE_NAMESPACE',
//                'PROVIDER_NAMESPACE',
//            ],
        ],
        'gitkeep' => true,
    ],
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Modules path
        |--------------------------------------------------------------------------
        |
        | This path used for save the generated module. This path also will be added
        | automatically to list of scanned folders.
        |
        */

        'modules' => base_path('app'),
        /*
        |--------------------------------------------------------------------------
        | Modules assets path
        |--------------------------------------------------------------------------
        |
        | Here you may update the modules assets path.
        |
        */

        'assets' => public_path('modules'),
        /*
        |--------------------------------------------------------------------------
        | The migrations path
        |--------------------------------------------------------------------------
        |
        | Where you run 'module:publish-migration' command, where do you publish the
        | the migration files?
        |
        */

        'migration' => base_path('database/migrations'),
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | 将在文件夹下建立以module名命名的子文件夹
        | generate key 为false则不会创建子文件夹
        */
        'generator' => [
            'controller' => ['path' => 'Http/Controllers', 'generate' => false],
            'service' => ['path' => 'Http/Services', 'generate' => true],
            'model' => ['path' => 'Model', 'generate' => true],
        ],
        /*
        |--------------------------------------------------------------------------
        | Part
        |--------------------------------------------------------------------------
        | 对于controller
        | 当不创建子文件夹时，会在对应当文件夹下创建文件夹
        | 当创建子文件时，会在对应子文件夹下创建Module[name]Controller
        | 对于service
        | 会在对应子文件夹下创建Module[name]Controller
        */
        'part' => [
            'admin' => ['name' => 'Admin', 'generate' => true, 'onlyService' => false],
            'index' => ['name' => 'Index', 'generate' => true, 'onlyService' => false],
            'inner' => ['name' => 'Inner', 'generate' => true, 'onlyService' => false],
            'member' => ['name' => 'Member', 'generate' => true, 'onlyService' => false],
            'common' => ['name' => 'Common', 'generate' => true, 'onlyService' => true],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | package commands
    |--------------------------------------------------------------------------
    |
    | Here you can define which commands will be visible and used in your
    | application. If for example you don't use some of the commands provided
    | you can simply comment them out.
    |
    */
    'commands' => [
        Commands\ControllerMakeCommand::class,
        Commands\ModuleMakeCommand::class,
        Commands\ServiceMakeCommand::class,
        Commands\ModelMakeCommand::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned. By default will scan vendor
    | directory. This is useful if you host the package in packagist website.
    |
    */

    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | Here is the config for composer.json file, generated by this package
    |
    */

    'composer' => [
        'vendor' => 'nwidart',
        'author' => [
            'name' => 'Nicolas Widart',
            'email' => 'n.widart@gmail.com',
        ],
        'composer-output' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here is the config for setting up caching feature.
    |
    */
    'cache' => [
        'enabled' => false,
        'key' => 'md-tech-modules',
        'lifetime' => 60,
    ],
    /*
    |--------------------------------------------------------------------------
    | Choose what laravel-modules will register as custom namespaces.
    | Setting one to false will require you to register that part
    | in your own Service Provider class.
    |--------------------------------------------------------------------------
    */
    'register' => [
        'translations' => true,
        /**
         * load files on boot or register method
         *
         * Note: boot not compatible with asgardcms
         *
         * @example boot|register
         */
        'files' => 'register',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activators
    |--------------------------------------------------------------------------
    |
    | You can define new types of activators here, file, database etc. The only
    | required parameter is 'class'.
    | The file activator will store the activation status in storage/installed_modules
    */
    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => base_path('modules_statuses.json'),
            'cache-key' => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],

    'activator' => 'file',
];
