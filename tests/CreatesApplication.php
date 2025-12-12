<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        Facade::setFacadeApplication($app);

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}