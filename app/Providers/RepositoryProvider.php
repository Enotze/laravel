<?php

namespace App\Providers;

use App\Repositories\ObjectsRepository;
use App\Repositories\ObjectsRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ObjectsRepository::class, ObjectsRepositoryEloquent::class);
    }
}
