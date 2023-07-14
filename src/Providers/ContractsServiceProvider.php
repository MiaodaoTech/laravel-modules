<?php

namespace MdTech\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use MdTech\Modules\Contracts\RepositoryInterface;
use MdTech\Modules\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
